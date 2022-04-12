  var CLIENT_ID = '267709590113-v4i5keof8q5gb07c85kbcl0in9tjuq1u.apps.googleusercontent.com';
  var API_KEY = 'AIzaSyCT-w9W-QZ6mEex9OUzmlKK9Z_Q5VVwBP0';

  // Array of API discovery doc URLs for APIs used by the quickstart
  var DISCOVERY_DOCS = ["https://www.googleapis.com/discovery/v1/apis/calendar/v3/rest"];

  // Authorization scopes required by the API; multiple scopes can be
  // included, separated by spaces.
  var SCOPES = "https://www.googleapis.com/auth/calendar";

  var googleAppointments = '';

  /** On load, called to load the auth2 library and API client library.*/
  function handleClientLoad() {
    gapi.load('client:auth2', initClient);
  }

  /* Initializes the API client library and sets up sign-in state listeners. */
  function initClient() {
    gapi.client.init({
      apiKey: API_KEY,
      clientId: CLIENT_ID,
      discoveryDocs: DISCOVERY_DOCS,
      scope: SCOPES
    }).then(function () {
      // Listen for sign-in state changes.
      gapi.auth2.getAuthInstance().isSignedIn.listen(updateSigninStatus);
      // Handle the initial sign-in state.
      updateSigninStatus(gapi.auth2.getAuthInstance().isSignedIn.get());
      listUpcomingEvents();
    }, function(error) {
      appendPre(JSON.stringify(error, null, 2));
    });
  }

  /*  Called when the signed in status changes, to update the UI appropriately. After a sign-in, the API is called. */
  function updateSigninStatus(isSignedIn) {
    if (isSignedIn) {
      $(".fc-googleSignIn-button").attr("style", "display: none !important");
      $(".fc-googleLogout-button").attr("style", "display: block !important");
      // insertEvent();
    } else {
      $(".fc-googleSignIn-button").attr("style", "display: block !important");
      $(".fc-googleLogout-button").attr("style", "display: none !important");
    }
  }

  function appendPre(message) {
    var pre = document.getElementById('content');
    var textContent = document.createTextNode(message + '\n');
    pre.appendChild(textContent);
  }

  function listUpcomingEvents() {
    var arrayEvents = [];
    gapi.client.idc
    gapi.client.calendar.events.list({
      'calendarId': 'primary',
      'timeMin': '2022-04-06T23:22:28.621Z',
      'showDeleted': false,
      'singleEvents': true,
      'maxResults': 50,
      'orderBy': 'startTime'
    }).then(function(response) {
      googleAppointments = response.result.items;
      for(let i = 0; i < googleAppointments.length; i++){
        if(!(googleAppointments[i].hasOwnProperty('extendedProperties') && googleAppointments[i].extendedProperties.hasOwnProperty('private') && googleAppointments[i].extendedProperties.private.hasOwnProperty('setByFullCalendar'))){
          eventTemplate(arrayEvents, googleAppointments[i]);
        }
      }

      calendar.addEventSource({
        color: '#12558C',
        textColor: 'white',
        backgroundColor:'#999',
        display:'block',
        borderColor: '#999',
        events: arrayEvents
      })
      
      calendar.refetchEvents(); 
    });
  }

  function eventTemplate(arrayEvents, googleAppointments){
    const { summary } = googleAppointments;
    let start, end;

    start = (googleAppointments.start.hasOwnProperty('date')) ? googleAppointments.start.date : googleAppointments.start.dateTime;
    end = (googleAppointments.end.hasOwnProperty('date')) ? googleAppointments.end.date : googleAppointments.end.dateTime;

    arrayEvents.push({
      title: summary,
      start: start,
      end: end
    });
  }