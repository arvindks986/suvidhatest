<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>

 <link rel="manifest" href="https://demo.eci.nic.in/pcceo/notification/firebase/manifest.json"/>
 <script src="https://www.gstatic.com/firebasejs/5.11.1/firebase-app.js"></script>
 <script src="https://www.gstatic.com/firebasejs/5.11.1/firebase-messaging.js"></script>
 <script src="https://www.gstatic.com/firebasejs/5.11.1/firebase.js"></script>

</head>
<body>

<div class="container">

  <button class="btn-default hitme">Hit Me</button>
  <h2>Horizontal form</h2>
  <form class="form-horizontal" action="/action_page.php">
    <div class="form-group">
      <label class="control-label col-sm-2" for="email">Email:</label>
      <div class="col-sm-10">
        <input type="email" class="form-control" id="email" placeholder="Enter email" name="email">
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-2" for="pwd">Password:</label>
      <div class="col-sm-10">          
        <input type="password" class="form-control" id="pwd" placeholder="Enter password" name="pwd">
      </div>
    </div>
    <div class="form-group">        
      <div class="col-sm-offset-2 col-sm-10">
        <div class="checkbox">
          <label><input type="checkbox" name="remember"> Remember me</label>
        </div>
      </div>
    </div>
    <div class="form-group">        
      <div class="col-sm-offset-2 col-sm-10">
        <button type="submit" class="btn btn-default">Submit</button>
      </div>
    </div>
  </form>
</div>
<script>

 $('.hitme').on('click', function () {
       alert('hi');
    });

   // Initialize Firebase
   var config = {
   apiKey: "AIzaSyDUXCqjnGljep9WXSHvnwmj1q3jTeQuIEs",
   authDomain: "suvidha-4f031.firebaseapp.com",
   databaseURL: "https://suvidha-4f031.firebaseio.com",
   projectId: "suvidha-4f031",
   storageBucket: "suvidha-4f031.appspot.com",
   messagingSenderId: "7963593522"
 };
   firebase.initializeApp(config);

   const messaging = firebase.messaging();
           messaging.onMessage(function(payload) {
               console.log("Message received. ", payload);
           });

   navigator.serviceWorker.register('https://demo.eci.nic.in/pcceo/notification/firebase/firebase-messaging-sw.js')
           .then((registration) => {
           messaging.useServiceWorker(registration);
           // Request permission and get token.....
           messaging.requestPermission()
                   .then(function() {
                       console.log('Notification permission granted.');
                       // Retrieve an Instance ID token for use with FCM.
                       messaging.getToken()
                               .then(function(currentToken) {
                                   if (currentToken) {
                                       console.log('Gotcha ' + currentToken);
                                       xmlHttp.send();
                                   } else {
                                       console.log('No Instance ID token available. Request permission to generate one.');
                                   }
                               })
                               .catch(function(err) {
                                   console.log('An error occurred while retrieving token. ', err);
                               });
                       // ...
                   })
                   .catch(function(err) {
                       console.log('Unable to get permission to notify.', err);
                   });
           });

</script>
</body>
</html>
