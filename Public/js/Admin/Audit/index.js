var tableApp=angular.module('mytableapp',[]);

tableApp.controller("tableController", function($scope,$http) {
    $scope.unverifiedInnovators=[];
    $scope.unverifiedInvestorPs=[];
    $scope.manageInvestors=[];
    $scope.manageInnovators=[];
    $scope.InnovatorsVerifiedTabClick=function()//click to load remote data
    {
      //window.alert("sdsd");
      $http.post('fetchInnovatorUnderVerified', {}).success(function(response){
              $scope.unverifiedInnovators=response;
            });
    };
    $scope.InnovatorsManageTabClick=function()//click to load remote data
    {
      //window.alert("sdsd");
      $http.post('fetchInnovator', {}).success(function(response){
              $scope.manageInnovators=response;
            });
    };
    $scope.InvestorsPsVerifiedTabClick=function()//click to load remote data
    {
      //window.alert("sdsd");
      $http.post('fetchInvestorPersonUnderVerified', {}).success(function(response){
              $scope.unverifiedInvestorPs=response;
            });
    };
    $scope.InvestorsManageTabClick=function()//click to load remote data
    {
      //window.alert("sdsd");
      $http.post('fetchInvestor', {}).success(function(response){
              $scope.manageInvestors=response;
            });
    };
});
