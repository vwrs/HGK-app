AWSCognito.config.region = "ap-northeast-1";
AWSCognito.config.credentials = new AWS.CognitoIdentityCredentials({
    IdentityPoolId: "ap-northeast-1:93cd62a1-0358-4296-80f9-7c10584fe0c0"
});
var poolData = {
    UserPoolId: "ap-northeast-1_lCn2ESmCm",
    ClientId: "3nbekhjm5a0a28eeocu9pj6e26"
};
var userPool = new AWSCognito.CognitoIdentityServiceProvider.CognitoUserPool(poolData);
var cognitoUser = userPool.getCurrentUser();

if (cognitoUser != null) {
    cognitoUser.getSession(function(err, sessresult) {
        if (sessresult) {
            console.log('You are now logged in.');
            cognitoUser.getUserAttributes(function(err, attrresult) {
                if (err) {
                    alert(err);
                    return;
                }
                $("#username").val(cognitoUser.username);
 
//                 for (i = 0; i < attrresult.length; i++) {
//                     if (attrresult[i].getName()=="email"){
//                       $("#email").html("EMail: " + attrresult[i].getValue());
//                     }
//                 }
 
                // Add the User's Id Token to the Cognito credentials login map.
                AWS.config.credentials = new AWS.CognitoIdentityCredentials({
                    IdentityPoolId: 'ap-northeast-1:ap-northeast-1:93cd62a1-0358-4296-80f9-7c10584fe0c0',
                    Logins: {
                        'cognito-idp.ap-northeast-1.amazonaws.com/ap-northeast-1_lCn2ESmCm': sessresult.getIdToken().getJwtToken()
                    }
                });
            });
        } else {
           var url = "signin.html";
           $(location).attr("href", url);
        }
    });
} else {
  var url = "signin.html";
  $(location).attr("href", url);
}
