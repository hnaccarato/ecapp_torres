if ('serviceWorker' in navigator) {
navigator.serviceWorker.register('/firebase-messaging-sw.js').then(function(registration) {
console.log('ServiceWorker registration successful with scope: ', registration.scope);
}).catch(function(err) {
    //registration failed :(
    console.log('ServiceWorker registration failed: ', err);
});
}else {
console.log('No service-worker on this browser');
}

// Your web app's Firebase configuration
var firebaseConfig = {
apiKey: "AIzaSyCjiAKKCG5DAGFyIKMBIe6v7KrYRjxqviU",
authDomain: "notibuilding.firebaseapp.com",
databaseURL: "https://notibuilding.firebaseio.com",
projectId: "notibuilding",
storageBucket: "notibuilding.appspot.com",
messagingSenderId: "521484079510",
appId: "1:521484079510:web:ea572f96334f20c6"
};
  // Initialize Firebase
firebase.initializeApp(firebaseConfig);

const messaging = firebase.messaging();
messaging
.requestPermission()
.then(function () {
console.log("Notification permission granted.");
return messaging.getToken()
})
.then(function(token) {
    console.log(token);
set_token(token) // guardo en base el token/
})
.catch(function (err) {
console.log("Unable to get permission to notify.", err);
});

messaging.onMessage(function(payload) {
    console.log("Message received. ", payload);
    alert("asd");
});

function set_token(token){
    $.post(base_url+controlador+'/registerWebToken',{token:token},function(data){

    }) 
}  
   

