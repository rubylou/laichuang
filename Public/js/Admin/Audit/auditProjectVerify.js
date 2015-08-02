var pro_id;

var init=function(pid)
{
	pro_id=pid;
}
var projectApp=angular.module('myprojectpapp',[]);

projectApp.controller("projectController", function($scope,$http) {
    $scope.project_id=pro_id;
    $scope.result="";
    $scope.verifynote="审核结束";
    
    $scope.submit=function(){
    	var url="receiveProjectVerifyResult?"+"project_id="+$scope.project_id+"&note="+$scope.verifynote+"&result="+$scope.result;
        var xmlhttp=createRequest();
        xmlhttp.onreadystatechange=function()
        {
            if (xmlhttp.readyState==4 && xmlhttp.status==200)
            {
                //alert(xmlhttp.responseText);
                //window.location.href="index";
                if(xmlhttp.responseText=='200')
                {
                    request_message($scope.project_id,'AUTHORIZATION','PROJECT','');
                    window.location.href="index";
                }else
                {
                    modalShow("alert_content","myModal","审核失败，请稍候再试！");
                }
            }else
            {
                modalShow("alert_content","myModal","审核失败，请稍候再试！");
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