// global variables
let dateObj = new Date();

// reference:
// https://www.youtube.com/watch?v=zvt8ff3d63Q
// https://www.youtube.com/watch?v=0T-awOV78DE
// https://stackoverflow.com/questions/29203358/login-on-same-page-check-session-without-reload

function signIn()
{
  // signIn page
  document.querySelector('.signInModal').style.display = 'flex';
  const formSignIn = 
  {
    username: document.getElementById('usernameSignIn'),
    password: document.getElementById('passwordSignIn'),
    submit: document.getElementById('signInBn')
  };
  // submit button
  formSignIn.submit.addEventListener('click', () =>
  {
    const request = new XMLHttpRequest();
    request.onload = () =>
    {
      try
      {
        response = JSON.parse(request.responseText);
      }
      catch(e)
      {
        console.error('Could not parse JSON!');
      }
      
      if(response.ok)   // sign in successfully
      {
        document.getElementById('logout').style.visibility='visible';
        document.getElementById('signIn').style.visibility='hidden';
        clearLoginForm();
        document.querySelector('.signInModal').style.display = 'none';
        document.getElementById('token').value = response.token;
        showDate(dateObj);
        console.log(document.getElementById('token').value);  
      }
      else  // fail to sign in 
      {
        document.getElementById('signInError').innerHTML=response.messages;
      }
    }  
    const requestData = `username=${encodeURIComponent(formSignIn.username.value)}&password=${encodeURIComponent(formSignIn.password.value)}`;
    request.open('post', 'login.php');
    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    request.send(requestData);
  });
}

function signUp()
{
  document.querySelector('.signInModal').style.display = 'none';
  document.querySelector('.signUpModal').style.display = 'flex';

  const formSignUp = 
  {
    username: document.getElementById('usernameSignUp'),
    password: document.getElementById('passwordSignUp'),
    rpassword: document.getElementById('rpasswordSignUp'),
    submit: document.getElementById('signUpBn')
  };

  // submit button
  formSignUp.submit.addEventListener('click', () =>
  {
    const request = new XMLHttpRequest();
    
    request.onload = () =>
    {
      try
      {
        response = JSON.parse(request.responseText);
      }
      catch(e)
      {
        console.error('Could not parse JSON!');
      }
      
      if(response.ok)   // sign in successfully
      {
        document.querySelector('.signInModal').style.display = 'flex';
        clearSignupForm();
        document.querySelector('.signUpModal').style.display = 'none';
        document.getElementById('signInError').innerHTML="Sign up successfully! Please log in.";
      }
      else
      {
        document.getElementById('signUpError').innerHTML=response.messages;
      }
    }  
    const requestData = `username=${encodeURIComponent(formSignUp.username.value)}&password=${encodeURIComponent(formSignUp.password.value)}&rpassword=${encodeURIComponent(formSignUp.rpassword.value)}`;
    request.open('post', 'signup.php');
    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    request.send(requestData);
  });
  
  // close button for sign up
  document.querySelector('.close2').addEventListener('click', function()
  {
    document.querySelector('.signInModal').style.display = 'none';
    document.querySelector('.signUpModal').style.display = 'none';
    clearLoginForm();
    clearSignupForm();
  });
}

function register()
{
  document.getElementById("signIn").addEventListener('click', signIn, false);

  // signUp button
  document.getElementsByName('signUp')[0].addEventListener('click', signUp, false);
    
  // close button for sign in 
  document.querySelector('.close1').addEventListener('click', function()
  {
    document.querySelector('.signInModal').style.display = 'none';
    clearLoginForm();
  });

  // logout
  if(!document.getElementById("logout").hidden)
  {
    document.getElementById("logout").addEventListener('click', logOut, false);
  }
}

function share()
{
  document.querySelector('.shareModal').style.display = 'flex';
  const form = 
  {
    token: document.getElementById('token'),
    friends: document.getElementById('friends'),
    submit: document.getElementById('shareBn')
  };

  form.submit.onclick = function()
  {
    const request = new XMLHttpRequest();
    request.onload = () =>
    {
      try
      {
        console.log(request.responseText);
        response = JSON.parse(request.responseText);
      }
      catch(e)
      {
        console.error('Could not parse JSON!');
      }
      
      if(response.ok)   // shared
      {
        document.getElementById('friends').value = "";  // clear share form
        document.querySelector('.shareModal').style.display = 'none';
      }
    }  
    const requestData = `token=${encodeURIComponent(form.token.value)}&friends=${encodeURIComponent(form.friends.value)}`;
    request.open('post', 'share.php');
    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    request.send(requestData);
  }

  // close button
  document.querySelector('.close3').addEventListener('click', function()
  {
    document.getElementById('friends').value = "";  // clear share form
    document.querySelector('.shareModal').style.display = 'none';
  });
}

function logOut()
{
  {
    const request = new XMLHttpRequest();
    request.onload = () =>
    {
      try
      {
        response = JSON.parse(request.responseText);
      }
      catch(e)
      {
        console.error('Could not parse JSON!');
      }
      if(response.ok)
      {
        document.getElementById('logout').style.visibility='hidden';
        document.getElementById('signIn').style.visibility='visible';
        document.getElementById('token').value = "";  // clear token value  
        showDate(dateObj);
      }
    
    }
    request.open('post', 'logout.php');
    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    request.send();
  }
}

//set the html of calendar
function renderHtml() {
  let calendar = document.getElementById("calendar");
  
  // set the title of the calendar
  //including the icon of prev and next month, and the information of current year and month;
  //classname is used for css to match
  let title = document.createElement("div");
  title.className = 'calendar_all_title';
  title.innerHTML = "<span class='prev_month' id='prevMonth'></span>" +
    "<span class='calendar-title' id='calendarTitle'></span>" +
    "<span class='next_month' id='nextMonth'></span>";
  calendar.appendChild(title);   
  let body = document.createElement("div");
   
  // set the body of the calendar
  //including months, weeks and days
  body.className = 'calendar-body-box';
   // catagary selection
  body.innerHTML += 'Category: <select class="category" name="chooseCategory" id="chooseCategory"><option value="8">All</option><option value="0">Study</option><option value="1">Work</option><option value="2">Sport</option><option value="3">Date</option><option value="4">Travel</option><option value="5">Family</option><option value="6">Fun</option><option value="7">Other</option></select>';
  
  // add login/out button
  body.innerHTML += '<div class="logButton"><button id="signIn">Sign In</button><button id="logout">Logout</button></div>';

     // add share button
 body.innerHTML += '<div class="shareButton"><button id="share">Share my Calendar</button></div>';

  //set the head of body
  let body_titleHtml = "<tr>" + 
            "<th>Sunday</th>" +
            "<th>Monday</th>" +
            "<th>Tuesday</th>" +
            "<th>Wednsday</th>" +
            "<th>Thursday</th>" +
            "<th>Friday</th>" +
            "<th>Saturday</th>" +
          "</tr>";
  //set the main part of the body
  let body_bodyHtml = "";
  for(let i = 0; i < 6; i++) {  
    body_bodyHtml += "<tr>" +"<td></td>" +"<td></td>" +"<td></td>" +"<td></td>" +"<td></td>" +"<td></td>" +"<td></td>" +"</tr>";
  }
  calendar.appendChild(body);
body.innerHTML += "<table id='calendarBody' class='calendar_body'>" + body_titleHtml + body_bodyHtml +"</table>";

  // add listener for category
  document.getElementById("chooseCategory").addEventListener("change", function(){
    showDate(dateObj);
  });

  // check if there exists login user
  const request = new XMLHttpRequest();
  request.onload = () =>
  {
    try
    {
      response = JSON.parse(request.responseText);
    }
    catch(e)
    {
      console.error('Could not parse JSON!');
    }
    
    if(response.ok)   // if login, give logout button
    {
      document.getElementById('logout').style.visibility='visible';
      document.getElementById('signIn').style.visibility='hidden';
      document.getElementById('token').value = response.token;
    }
    else  // no user login, give sign in/up button
    {
      document.getElementById('logout').style.visibility='hidden';
      document.getElementById('signIn').style.visibility='visible';
    }
  }  
  request.open('post', 'checkUser.php');
  request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  request.send();
}

function showDate(dateObj) {
  let c_year = dateObj.getYear()+1900;
  let c_month = dateObj.getMonth()+1;
  //transfer the date information into a string so that it will be extracted easily
  let dateStr = getDateStr(dateObj);
  // set the information of calendarTitle
  let titleStr = "Year: " + dateStr.substr(0, 4) + " Month:" + dateStr.substr(4,2);
  document.getElementById("calendarTitle").innerText = titleStr;
  // get category value
  let showCategory = document.getElementById("chooseCategory").options[document.getElementById("chooseCategory").selectedIndex].text;
  

  // get the first day of a month
  let firstDay = new Date(c_year, c_month-1, 1);
  let cur_td = document.getElementById("calendarBody").getElementsByTagName("td");
  
  for(let i = 0; i < cur_td.length; i++) {
    // delete event table if exists
    if(document.getElementById(i) != null)
    {
      cur_td[i].removeChild(document.getElementById(i))
    }

    let thisDay = new Date(c_year, c_month-1, i - firstDay.getDay() + 1);
    let thisDayStr = getDateStr(thisDay);
    
    let eventList = document.createElement("ul");
    eventList.id = i;
    let dateDiv = document.createElement("li");

    dateDiv.innerHTML = thisDay.getDate();
    eventList.appendChild(dateDiv);
    // add eventListener for date.div
    dateDiv.addEventListener("click", function()
    {
      // send ajax request to check if user log in
      const request = new XMLHttpRequest();
      let hasLogin = false;
      request.onload = () =>
      {
        try
        {
          response = JSON.parse(request.responseText);
        }
        catch(e)
        {
          console.error('Could not parse JSON!');
          
        }
        if(response.ok)   // has login user
        {
          document.getElementById('closeForm').onclick = function () 
          {
            closeForm();
          }
          timeInfo = thisDayStr.substr(0,4) + '-' + thisDayStr.substr(4,2) + '-' + thisDayStr.substr(6,2);
          document.getElementById("eventDate").innerHTML = timeInfo;
          document.getElementById("eventForm").style.display = "block";
          clearAddForm();
          addEvent();
        }
      }  
      request.open('post', 'checkUser.php');
      request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
      request.send();
    });

    // check if user login
    const request = new XMLHttpRequest();
    request.onload = () =>
    {
      try
      {
        response = JSON.parse(request.responseText);
      }
      catch(e)
      {
        console.error('Could not parse JSON!');
      }
      
      if(response.ok)   // has login user
      {
        // get all events of current user of current date
        const request = new XMLHttpRequest();
        request.onload = () =>
        {
          try
          {
            response = JSON.parse(request.responseText);
          }
          catch(e)
          {
            console.error('Could not parse JSON!');
          }
          
          if(response.ok)   // has login user
          {
            // for each event, create a td and append to dateDiv.innerHTML
            let ids = response.eventsID;
            let titles = response.eventsTitle;
            let times = response.eventsTime;
            let tags = response.eventsTag;
            for(let i = 0; i < ids.length; i++)
            {
              let event = document.createElement("li");
              event.id = ids[i];
              
              event.innerHTML = times[i].substr(0,5) + '  ' + titles[i];

              // add "edit" event listener for current event
              event.addEventListener("click", function(event)
              {
                // send ajax request to check if user log in
                const request = new XMLHttpRequest();
                let hasLogin = false;
                request.onload = () =>
                {
                  try
                  {
                    response = JSON.parse(request.responseText);
                  }
                  catch(e)
                  {
                    console.error('Could not parse JSON!');
                    
                  }
                  if(response.ok)   // has login user
                  {
                    console.log("check user in edit");
              
                    // delete current event
                    document.getElementById('editCloseForm').onclick = function () {
                      closeForm();
                    }
                    timeInfo = thisDayStr.substr(0,4) + '-' + thisDayStr.substr(4,2) + '-' + thisDayStr.substr(6,2);
                    document.getElementById("editEventDate").innerHTML = timeInfo;
                    document.getElementById("changeDate").value = timeInfo;
                    document.getElementById("editEventForm").style.display = "block";
                    document.getElementById("editEventTitle").value = titles[i];
                    
                    // set default value for time
                    let t = parseInt(times[i].substr(0,2));
                    document.getElementById('editEventTime').options[t].setAttribute('selected', 'selected');

                    // set default value for category
                    let index = 0;
                    if (tags[i] == 'Work')
                    {
                      index = 1;
                    }
                    else if (tags[i] == 'Sport')
                    {
                      index = 2;
                    }
                    else if (tags[i] == 'Date')
                    {
                      index = 3;
                    }
                    else if (tags[i] == 'Travel')
                    {
                      index = 4;
                    }
                    else if (tags[i] == 'Family')
                    {
                      index = 5;
                    }
                    else if (tags[i] == 'Fun')
                    {
                      index = 6;
                    }
                    else if (tags[i] == 'Other')
                    {
                      index = 7;
                    }
                    document.getElementById('editEventCategory').options[index].setAttribute('selected','selected');
                    editEvent(event.target.id);
                    deleteEvent(event.target.id);
                  }
                }  
                request.open('post', 'checkUser.php');
                request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                request.send();
              });
              eventList.appendChild(event);
            }
          }
        }
        const requestData = `date=${encodeURIComponent(thisDayStr)}&category=${encodeURIComponent(showCategory)}`;
        request.open('post', 'getEvents.php');
        request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        request.send(requestData);
      }
    }  
    request.open('post', 'checkUser.php');
    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    request.send();
    cur_td[i].appendChild(eventList);

    if(thisDayStr == getDateStr(new Date())) {
      cur_td[i].className = 'currentDay';
    }else if(thisDayStr.substr(0, 6) == getDateStr(firstDay).substr(0, 6)) {
      cur_td[i].className = 'currentMonth'; 
    }else {
      cur_td[i].className = 'otherMonth';
    }
  }
}

//change the date to str
function getDateStr(date) {
  let c_year = date.getYear() + 1900;
  //month start from 0
  let c_month = date.getMonth() + 1;
  let d = date.getDate();
  c_month = (c_month > 9) ? ("" + c_month) : ("0" + c_month);
  d = (d > 9) ? ("" + d) : ("0" + d);
  return c_year +  c_month +d;
}

//enable the icon which can change the month
function monthChange() {
  console.log("month change begin");
  document.getElementById("prevMonth").addEventListener("click",toPrevMonth);
  document.getElementById("nextMonth").addEventListener("click",toNextMonth);
}

function toPrevMonth() {
  dateObj.setMonth(dateObj.getMonth() - 1);
  dateObj.setDate(1);
  dateObj.setFullYear(dateObj.getYear() + 1900);
  showDate(dateObj);
}

function toNextMonth() {
  dateObj.setMonth(dateObj.getMonth() + 1);
  dateObj.setDate(1);
  dateObj.setFullYear(dateObj.getYear() + 1900);
  showDate(dateObj);
}

function addEvent(){

  const formAddEvent = 
  {
    token: document.getElementById('token'),
    eventTitle: document.getElementById("eventTitle"),
	  eventDate: document.getElementById("eventDate"),
	  eventTime: document.getElementById("eventTime"),
    eventCategory: document.getElementById("eventCategory"),
    eventGroup: document.getElementById("addGroup"),
    submit: document.getElementById('addEvent')
  };

    formAddEvent.submit.onclick = function(){
    let request = new XMLHttpRequest();
    request.onload = () =>
    {
      try
      {
        console.log(request.responseText);
        const response = JSON.parse(request.responseText);
      }
      catch(e)
      {
        console.error('Could not parse JSON!');
      }
      
      if(response.ok)   // add successfully
      {
        request.abort();
        document.getElementById("eventForm").style.display = "none";
        showDate(dateObj);
      }
    }  
    const requestData = 
    `token=${encodeURIComponent(formAddEvent.token.value)}&eventTitle=${encodeURIComponent(formAddEvent.eventTitle.value)}&eventDate=${encodeURIComponent(formAddEvent.eventDate.innerText)}&eventTime=${encodeURIComponent(formAddEvent.eventTime.options[document.getElementById("eventTime").selectedIndex].text)}&eventCategory=${encodeURIComponent(formAddEvent.eventCategory.options[document.getElementById("eventCategory").selectedIndex].text)}&friends=${encodeURIComponent(formAddEvent.eventGroup.value)}`;
    request.open('post', 'addEvent.php', true);
    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    request.send(requestData);
  }
}


// get info in pop up window and edit by php when "edit" button clicked
function editEvent(id){
  //ajax transfer event info
  const formEditEvent = 
  {
    token: document.getElementById('token'),
    eventTitle: document.getElementById("editEventTitle"),
	  eventDate: document.getElementById("changeDate"),
	  eventTime: document.getElementById("editEventTime"),
	  eventCategory: document.getElementById("editEventCategory"),
    submit: document.getElementById('editEvent')
  };

  // when edit button clicked
  formEditEvent.submit.onclick = function()
  {
    console.log('edit');
    const request = new XMLHttpRequest();
    
    request.onload = () =>
    {
      try
      {
        response = JSON.parse(request.responseText);
      }
      catch(e)
      {
        console.error('Could not parse JSON!');
      }
      
      if(response.ok)   // edit successfully
      {
        //location.reload();
        document.getElementById("editEventForm").style.display = "none";
        showDate(dateObj);
      }
      else
      {
        document.getElementById('editEventError').innerHTML=response.messages;
      }
    }  
    const requestData = 
    `token=${encodeURIComponent(formEditEvent.token.value)}&id=${encodeURIComponent(id)}&eventTitle=${encodeURIComponent(formEditEvent.eventTitle.value)}&eventDate=${encodeURIComponent(formEditEvent.eventDate.value)}&eventTime=${encodeURIComponent(formEditEvent.eventTime.options[document.getElementById("editEventTime").selectedIndex].text)}&eventCategory=${encodeURIComponent(formEditEvent.eventCategory.options[document.getElementById("editEventCategory").selectedIndex].text)}`;
    request.open('post', 'editEvent.php');
    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    request.send(requestData);
  }
}

//delete event function
function deleteEvent(eventID){

  // when edit button clicked
  document.getElementById('deleteEvent').onclick = function()
  {
    // send ajax request to delete current event
    const request = new XMLHttpRequest();
    request.onload = () =>
    {
      try
      {
        response = JSON.parse(request.responseText);
      }
      catch(e)
      {
        console.error('Could not parse JSON!');
        
      }
      if(response.ok)   // delete successfully
      {
        //location.reload();
        document.getElementById("editEventForm").style.display = "none";
        showDate(dateObj);
      }
    }  
    const requestData = `token=${encodeURIComponent(document.getElementById('token').value)}&id=${encodeURIComponent(eventID)}`;
    request.open('post', 'deleteEvent.php');
    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    request.send(requestData);
  }
} 

// close current pop up window
function closeForm() 
{
  document.getElementById("eventForm").style.display = "none";
  document.getElementById("editEventForm").style.display = "none";
}

function clearAddForm()
{
  document.getElementById("eventTitle").value = "";
  document.getElementById("eventTime").options[0].setAttribute('selected','selected');
  document.getElementById("eventCategory").options[0].setAttribute('selected','selected');
  document.getElementById("addGroup").value = "";
}

function clearLoginForm()
{
  document.getElementById('usernameSignIn').value = "";
  document.getElementById('passwordSignIn').value = "";
  document.getElementById('signInError').innerHTML = "";

}

function clearSignupForm()
{
  document.getElementById('usernameSignUp').value = "";
  document.getElementById('passwordSignUp').value = "";
  document.getElementById('rpasswordSignUp').value = "";
  document.getElementById('signInError').innerHTML = "";
  document.getElementById('signUpError').innerHTML = "";
}


function myCalender()
{
  renderHtml();
  showDate(dateObj);
  monthChange();
  register();
  // add listener for share
  document.getElementById("share").addEventListener("click", share, false);
}
// ---------------- main ---------------
document.addEventListener("DOMContentLoaded", myCalender, false);