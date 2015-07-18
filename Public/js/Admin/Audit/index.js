var tableApp=angular.module('mytableapp',[]);

tableApp.controller("tableController", function($scope,$http) {
    $scope.unverifiedInnovators=[];
    $scope.unverifiedInvestorPs=[];
    $scope.InnovatorsVerifiedTabClick=function()//click to load remote data
    {
      //window.alert("sdsd");
      $http.post('fetchInnovatorUnderVerified', {}).success(function(response){
              $scope.unverifiedInnovators=response;
            });
    };
    $scope.InvestorsPsVerifiedTabClick=function()//click to load remote data
    {
      //window.alert("sdsd");
      $http.post('fetchInvestorPersonUnderVerified', {}).success(function(response){
              $scope.unverifiedInvestorPs=response;
            });
    };
});
