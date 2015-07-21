var user;
var jobs;
var type;
var init=function(u,j)
{
	user=u[0];
	jobs=j;
	if(user.user_type==1)
		type="风险投资人";
	else
		type="天使投资人";
}
var invesPApp=angular.module('myinvespapp',[]);

invesPApp.controller("userController", function($scope,$http) {
    $scope.userInfo=user;
    $scope.jobInfo=jobs;
    $scope.type=type;
    $scope.result="";
    $scope.verifynote="审核结束";
    
    $scope.submit=function(){
    	var url="receiveInverstorPVerifyResult?"+"user_id="+$scope.userInfo.user_id+"&note="+$scope.verifynote+"&result="+$scope.result;
        var xmlhttp=createRequest();
        xmlhttp.onreadystatechange=function()
  		{
  			if (xmlhttp.readyState==4 && xmlhttp.status==200)
    		{
    			//alert(xmlhttp.responseText);
    			window.location.href="index";
    		}
		}
		xmlhttp.open("GET",url,true);
		xmlhttp.send();
    	/*$http.post('receiveInverstorPVerifyResult'+'?', {
    		"user_id":$scope.userInfo.user_id,
    		"result":$scope.result,
    		"note":$scope.note
    	}).success(function(response){
    		  alert(response);
              window.location.href="index";
            });*/
    };

});
