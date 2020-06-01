 // mod6 2.0
 // Require the packages we will use:
var http = require("http"), 
socketio = require("socket.io"),
fs = require("fs");

// Listen for HTTP connections.  This is essentially a miniature static file server that only serves our one file, client.html:
var app = http.createServer(function(req, resp){
// This callback runs when a new connection is made to our HTTP server.

    fs.readFile("client.html", function(err, data){
        // This callback runs when the client.html file has been read from the filesystem.
        
        if(err) return resp.writeHead(500);
        resp.writeHead(200);
        resp.end(data);
    });
});
app.listen(3456);

// Do the Socket.IO magic:
var io = socketio.listen(app);

// list data members
let users = {}; // username -> socketID, currRoom it is in
let rooms = {}; // roomName -> []users in current room, []admin, []banned users in curr room, password?

// display/update "all room list" on web
function displayAllRoomsList()
{
    // display all rooms
    let result = []
    for(const r in rooms)
    {
        result.push([r, rooms[r][1][0]]);    // roomname, owner
    }
    io.sockets.emit("displayAllRoom",{message: result});    // update rooms info to all clients
}

// update given user's room info to given new room
function updateUserRoomInfo(user, newRoom)
{
    if(users[user].length == 2) // if current user previous has a room, switch to new on
    {
        users[user][1] = newRoom;
    }
    else    // curr user previous does not have a room
    {
        users[user].push(newRoom);
    }
}

// funcionalities
io.sockets.on("connection", function(socket)    // This callback runs when a new Socket.IO connection is established.
{
    // add user
    socket.on('addUser', function(data) 
    {   
        let name = data['message'];
        if(!(name in users)) // if user did not exist
        {
            if (name == "")
            {
                socket.emit("logErrorMsg",{message: "Username can not be empty."})
            }
            else    // add user
            {
                users[name]=[socket.id];
                socket.emit("logIn",{message: name});
            }
        }
        else    // user already exists
        {
            socket.emit("logErrorMsg",{message: "Username is used, please select a different name."})
        }
    });

    // Create a new room
    // data: newRoomName, newRoomPassword, creater/user, oldRoom
    socket.on('createRoom', function(data) 
    {   
        const name = data['message'][0];    // roomName
        const user = data['message'][2];
        const currRoom = data['message'][3];
        
        if(!(name in rooms)) // check if room did not exist
        {
            // quit last room if has last room / currently is in a room
            if(currRoom in rooms)
            {
                const i = rooms[currRoom][0].indexOf(user);
                const j = rooms[currRoom][1].indexOf(user);
                if(i > -1) // is user exists in specified room, remove it
                {
                    rooms[currRoom][0].splice(i, 1);
                    
                    if(rooms[currRoom][0].length == 0)  // if nobody in this room, delete this room
                    {
                        delete rooms[currRoom];
                    }
                    else // notify each other users
                    {   
                        if( j > -1 ) // currUser is admin 
                        {
                            rooms[currRoom][1].splice(j, 1);
                            if(rooms[currRoom][1].length == 0)  // if nobody in admin, let users[0] be in admin
                            {
                                rooms[currRoom][1].push(rooms[currRoom][0][0]);
                            }
                        }
                        const otherUsers = rooms[currRoom][0];
                        for(let k = 0; k < otherUsers.length; k++)
                        {
                            socket.broadcast.to( users[otherUsers[k]][0] ).emit("updateRoom",{message: [rooms[currRoom][0], rooms[currRoom][1]]});
                        }
                    }
                    displayAllRoomsList();
                }
            }

            // add new room to room list, curr user is owner
            rooms[name]=[[user], [user], [],  data['message'][1]];// currentUser[], admin[], ban[], password
            // add new room to userRoom list
            updateUserRoomInfo(user, name);

            socket.emit("createErrorMsg",{message: "Room created successfully."});
            socket.emit("showRoom",{message: [name, [user], [user]]});    // show chat window of this new room to current user

            displayAllRoomsList();
        }
        else    // room already exists
        {
            socket.emit("createErrorMsg",{message: "Room already exist. Please Join."})
        }
    });

    // join a exist room
    socket.on('joinRoom', function(data) 
    {   
        const newRoom = data['message'][0];
        const password = data['message'][1];
        const currUser = data['message'][2];
        const currRoom = data['message'][3];
        if(newRoom in rooms) // if room exists
        {
            if(newRoom == currRoom) // user try to join same room
            {
                socket.emit("joinErrorMsg",{message: "You are already in this room."}); 
            }
            else
            {
                const banIndex = rooms[newRoom][2].indexOf(currUser);
                if(banIndex > -1)    // curr user is in the ban list of the new room, can not let curr user join the new room
                {
                    socket.emit("joinErrorMsg",{message: "You are banned from this room. Not able to join."}); 
                }
                else if((rooms[newRoom][3] != "") && (password != rooms[newRoom][3]))   // if room has password and password not match
                {
                    if(password == "") // user did not input password
                    {
                        socket.emit("joinErrorMsg",{message: "This is a private room. Please enter password."});
                    }
                    else    // password is wrong / not match
                    {
                        socket.emit("joinErrorMsg",{message: "Incorrect Password. Please try again."});
                    }
                }
                else    // user join the new room
                {
                    // quit last room
                    if(currRoom in rooms)   // if has last room / currently is in a room
                    {
                        const i = rooms[currRoom][0].indexOf(currUser);
                        const j = rooms[currRoom][1].indexOf(currUser);
                        if(i > -1) // is user exists in specified room, remove it
                        {
                            rooms[currRoom][0].splice(i, 1);
                            
                            if(rooms[currRoom][0].length == 0)  // if nobody in this room, delete this room
                            {
                                delete rooms[currRoom];
                            }
                            else // notify each other users
                            {   
                                if( j > -1 ) // currUser is admin 
                                {
                                    rooms[currRoom][1].splice(j, 1);
                                    if(rooms[currRoom][1].length == 0)  // if nobody in admin, let users[0] be in admin
                                    {
                                        rooms[currRoom][1].push(rooms[currRoom][0][0]);
                                    }
                                }
                                const otherUsers = rooms[currRoom][0];
                                for(let k = 0; k < otherUsers.length; k++)
                                {
                                    socket.broadcast.to( users[otherUsers[k]][0] ).emit("updateRoom",{message: [rooms[currRoom][0], rooms[currRoom][1]]});
                                }
                            }
                            displayAllRoomsList();
                        }
                    }
                    // update currUser's currRoom info
                    updateUserRoomInfo(currUser, newRoom);

                    // update chat room info to all users (include joined user) in new room 
                    rooms[newRoom][0].push(currUser); // add currUser to the room's users list
                    const newUsers = rooms[newRoom][0];
                    // update roomInfo to all other users
                    for(let i = 0; i < newUsers.length; i++)
                    {
                        socket.broadcast.to( users[newUsers[i]][0] ).emit("updateRoom",{message: [rooms[newRoom][0], rooms[newRoom][1]]});
                    }
                    socket.emit("showRoom",{message: [newRoom, rooms[newRoom][0], rooms[newRoom][1]]}); // update room info to curr user
                    socket.emit("joinErrorMsg",{message: "Room joined successfully."}); // display successful msg to joined user

                    displayAllRoomsList();
                }
            }
        }
        else    // room does not exist
        {
            socket.emit("joinErrorMsg",{message: "Room does not exist. Please create it."})
        }
    });

    // remove specified person from specified room
    // data: roomName, personUsername
    socket.on('kick', function(data) 
    {   
        const currRoom = data['message'][0];
        const kickUser = data['message'][1];
        const i = rooms[currRoom][0].indexOf(kickUser); // index of kickUser in userList
        const k = rooms[currRoom][1].indexOf(kickUser); // index of kickUser in adminList
        if(i > -1)
        {
            rooms[currRoom][0].splice(i, 1);    // remove given person from users list of given room

            if(k > -1)
            {
                rooms[currRoom][1].splice(k, 1);    // remove given person from admin list of given room
            }

            // update room info for all other users except owner
            const otherUsers = rooms[currRoom][0];
            for(let j = 0; j < otherUsers.length; j++)
            {
                socket.broadcast.to( users[otherUsers[j]][0] ).emit("updateRoom",{message: [rooms[currRoom][0], rooms[currRoom][1]]});
            }
            // update room info to owner
            socket.emit("updateRoom",{message: [rooms[currRoom][0], rooms[currRoom][1]]});

            // remove room for kick out user and update his view
            users[kickUser].splice(1, 1);
            socket.broadcast.to( users[kickUser][0] ).emit("showRoom",{message: [ "", [], [] ]});   // msg: currRoomName="", usersInNullRoom=[], adminsInNullRoom=[]
        }
    });

    // remove specified person and add its username in ban list at specified room 
    // data: roomName, personUsername
    socket.on('ban', function(data) 
    {   
        const currRoom = data['message'][0];
        const banUser = data['message'][1];
        const i = rooms[currRoom][0].indexOf(banUser);
        const k = rooms[currRoom][1].indexOf(banUser);
        if(i > -1)
        {
            rooms[currRoom][0].splice(i, 1);    // remove given person from users list of given room

            if(k > -1)  // remove banUser from admin
            {
                rooms[currRoom][1].splice(k, 1);
            }

            rooms[currRoom][2].push(banUser);   // add given person to ban list
            // update room info for all other users except owner
            const otherUsers = rooms[currRoom][0];
            for(let j = 0; j < otherUsers.length; j++)
            {
                socket.broadcast.to( users[otherUsers[j]][0] ).emit("updateRoom",{message: [rooms[currRoom][0], rooms[currRoom][1]]});
            }
            // update room info to owner
            socket.emit("updateRoom",{message: [rooms[currRoom][0], rooms[currRoom][1]]});

            // remove room for kick out user and update his view
            users[banUser].splice(1, 1);
            socket.broadcast.to( users[banUser][0] ).emit("showRoom",{message: [ "", [], [] ]});   // msg: currRoomName="", usersInNullRoom=[], adminsInNullRoom=[]
        }
    });


    // add given person to admin list of given room
    // data: roomName, personUsername
    socket.on('addAdmin', function(data) 
    {   
        const currRoom = data['message'][0];
        const currUser = data['message'][1];
        const i = rooms[currRoom][0].indexOf(currUser);
        if(i > -1)  // if user exists in currRoom
        {
            rooms[currRoom][1].push(currUser);    // add currUser into admin list

            // update room info for all other users except owner
            const otherUsers = rooms[currRoom][0];
            for(let j = 0; j < otherUsers.length; j++)
            {
                socket.broadcast.to( users[otherUsers[j]][0] ).emit("updateRoom",{message: [rooms[currRoom][0], rooms[currRoom][1]]});
            }
            // update room info to owner
            socket.emit("updateRoom",{message: [rooms[currRoom][0], rooms[currRoom][1]]});
        }
    });

    // remove given person to admin list of given room
    // data: roomName, personUsername
    socket.on('removeAdmin', function(data) 
    {   
        const currRoom = data['message'][0];
        const currUser = data['message'][1];
        const i = rooms[currRoom][0].indexOf(currUser);
        if(i > -1)  // if user exists in currRoom
        {
            rooms[currRoom][1].splice(i, 1);    // remove currUser into admin list

            // update room info for all other users except owner
            const otherUsers = rooms[currRoom][0];
            for(let j = 0; j < otherUsers.length; j++)
            {
                socket.broadcast.to( users[otherUsers[j]][0] ).emit("updateRoom",{message: [rooms[currRoom][0], rooms[currRoom][1]]});
            }
            // update room info to owner
            socket.emit("updateRoom",{message: [rooms[currRoom][0], rooms[currRoom][1]]});
        }
    });

    // display all rooms
    socket.on('displayAllRooms', function(data) 
    {   
        displayAllRoomsList();
    });

    // get message from a user, check message type and then display on all/private one user's chat window
    socket.on('message_to_server', function(data) {
        const words = data['message'][0];   // message content
        const who = data['message'][1]; // send to specific person based on given value, send to all if public
        const roomName = data['message'][2];
        const allUsers = rooms[roomName][0];
        const sender = data['message'][3];
        const msg = [sender, words];
        let currId = -1;
        if (who == "public")   // send to all
        {
            socket.emit('displayMsg', {message:msg});   // send to sender
            for(let i = 0; i < allUsers.length; i++)    // send to all users in curr room
            {
                currId = users[allUsers[i]][0];
                socket.broadcast.to(currId).emit('displayMsg', {message:msg});
            }
        }
        else    // send to one person
        {
            const j = allUsers.indexOf(who);
            if (j > -1)
            {
                // send to specific person
                currId = users[allUsers[j]][0];
                socket.broadcast.to(currId).emit('displayMsg', {message:msg});
                // send to sender
                socket.emit("displayMsg",{message: msg});
            }
        }
    });

    // send image
    // data: img, sender, receiver, room
    socket.on('userImg', function (data) {
        const img = data['message'][0];
        const sender = data['message'][1];
        const who = data['message'][2]; // send to specific person based on given value, send to all if public
        const roomName = data['message'][3];

        const allUsers = rooms[roomName][0];
        const msg = [img, sender];
        let currId = -1;
        if (who == "public")   // send to all
        {
            socket.emit('userImgs', {message:msg});   // send to sender
            for(let i = 0; i < allUsers.length; i++)    // send to all users in curr room
            {
                currId = users[allUsers[i]][0];
                socket.broadcast.to(currId).emit('userImgs', {message:msg});
            }
        }
        else    // send to one person
        {
            const j = allUsers.indexOf(who);
            if (j > -1)
            {
                // send to specific person
                currId = users[allUsers[j]][0];
                socket.broadcast.to(currId).emit('userImgs', {message:msg});
                // send to sender
                socket.emit("userImgs",{message: msg});
            }
        }
    });

    // disconnect
    socket.on('disconnect', function(){
        let currUser = "";
        for(let username in users)
        {
            if(users[username][0] == socket.id)
            {
                currUser = username;
                break;
            }
        }
        if((currUser != "") && (users[currUser].length == 2))  // if user has a room, delete currUser from room
        {
            const currRoom = users[currUser][1];
            const i = rooms[currRoom][0].indexOf(currUser);
            const j = rooms[currRoom][1].indexOf(currUser);
            if(i > -1) // is user exists in specified room, remove it
            {
                rooms[currRoom][0].splice(i, 1);
                
                if(rooms[currRoom][0].length == 0)  // if nobody in this room, delete this room
                {
                    delete rooms[currRoom];
                }
                else // notify each other users
                {   
                    if( j > -1 ) // currUser is admin 
                    {
                        rooms[currRoom][1].splice(j, 1);
                        if(rooms[currRoom][1].length == 0)  // if nobody in admin, let users[0] be in admin
                        {
                            rooms[currRoom][1].push(rooms[currRoom][0][0]);
                        }
                    }
                    const otherUsers = rooms[currRoom][0];
                    for(let k = 0; k < otherUsers.length; k++)
                    {
                        socket.broadcast.to( users[otherUsers[k]][0] ).emit("updateRoom",{message: [rooms[currRoom][0], rooms[currRoom][1]]});
                    }
                }
                displayAllRoomsList();
            }
        }
        delete users[currUser];    // delete currUser info
    });
});