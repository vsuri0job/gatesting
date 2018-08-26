/*****
Bytutorial.com - online community to share articles for web and mobile programming and designers.
Author: Andy Suwandy

NOTE: Please change the CLIENT ID by creating your own app in google app console.
API Key is not needed but has been placed as an example.

In order to work in your local computer, please make sure the CLIENT ID is correct and the script origins match with your local computer url.
otherwise you will get an error message saying the url you try to load does not match with origin source.
****/

/******************** GLOBAL VARIABLES ********************/
var SCOPES = ['https://www.googleapis.com/auth/analytics.readonly'];
var CLIENT_ID = '527941907632-81gtlvea9ef8htq37sk9palefvmk2sqo.apps.googleusercontent.com';
var API_KEY = '';

var byTutorialAnalytics = {
	
	/******************** AUTHENTICATION ********************/
	handleClientLoad: function() {
		// Load the API client and auth2 library
		gapi.load('client:auth2', byTutorialAnalytics.initClient);
	},

	//authorize apps
	initClient: function() {
		gapi.client.init({
			//apiKey: API_KEY, //THIS IS OPTIONAL AND WE DONT ACTUALLY NEED THIS, BUT I INCLUDE THIS AS EXAMPLE
			clientId: CLIENT_ID,
			scope: SCOPES.join(' ')
		}).then(function () {
		  // Listen for sign-in state changes.
		  gapi.auth2.getAuthInstance().isSignedIn.listen(byTutorialAnalytics.updateSigninStatus);
		  // Handle the initial sign-in state.
		  byTutorialAnalytics.updateSigninStatus(gapi.auth2.getAuthInstance().isSignedIn.get());
		});
	},

	//check the return authentication of the login is successful, we display the account panel box and hide the login box.
	updateSigninStatus: function(isSignedIn) {
		if (isSignedIn) {
			$("#login-box").hide();
			$("#account-panel").removeClass("hidden").show();
			byTutorialAnalytics.queryAccounts();
		} else {
			$("#login-box").removeClass("hidden").show();
		}
	},

	handleAuthClick: function(event) {
		gapi.auth2.getAuthInstance().signIn();
	},

	handleSignoutClick: function(event) {
		if(confirm("Are you sure you want to logout?")){
			gapi.auth2.getAuthInstance().signOut();
			$("#account-panel, #result").hide();
			$(".result-box").html("");
		}
	},
	/******************** END AUTHENTICATION ********************/
	
	//function to query google analytics account
	queryAccounts: function () {
		byTutorialAnalytics.showLoading();
		// Load the Google Analytics client library.
		gapi.client.load('analytics', 'v3').then(function() {
			// Get a list of all Google Analytics accounts for this user
			var request = gapi.client.analytics.management.accountSummaries.list();
			request.execute(byTutorialAnalytics.handleAccountSummaryResponse);
		});
	},
	
	//handle the response of account summary
	handleAccountSummaryResponse: function (response) {
	  if (response && !response.error) {
		if (response.items) {
		  byTutorialAnalytics.buildAccountSummariesCombo(response.items);
		}
	  } else {
		console.log('There was an error: ' + response.message);
		byTutorialAnalytics.hideLoading();
	  }
	},
	
	//build the account summaries combo box
	buildAccountSummariesCombo: function(accounts) {

	  for (var i = 0, account; account = accounts[i]; i++) {
	  	
	  	//console.log(account);

		var webProperties_length = 0;	
		var profiles_length = 0;

		if (typeof account.webProperties !== 'undefined' && account.webProperties.length > 0) {
		    // the array is defined and has at least one element

			//if (typeof account.webProperties[0].profiles !== 'undefined' && account.webProperties[0].profiles > 0) {
		    // the array is defined and has at least one element

				profiles_length = account.webProperties[0].profiles.length;
			//}

			webProperties_length = account.webProperties.length;
		}

		console.log(webProperties_length+ ' | ' +profiles_length);

		if(webProperties_length > 0 && profiles_length > 0){
			$("#cboAccount").append("<option value='" + account.webProperties[0].profiles[0].id + "'>" + account.name + "</option>");
		}
	  }
	  $("#account-panel").removeClass("hidden").show();
	  byTutorialAnalytics.hideLoading()
	},
	
	//function to query google analytics data 
	queryAnalytics: function () {
		var id = $("#cboAccount").val();
		var expressions = [];
		
		//we loop through the checkbox and append it to expression array
		$(".chk-data").each(function(item,index){
			if($(this).is(":checked")){
				var expressionData = {
					expression: $(this).attr("data-id")
				}
				expressions.push(expressionData);
			}
		});
		
		//get the report query by passing the period time and expression daa.
		byTutorialAnalytics.showLoading();
		//if it is yesterday comparison, we have to set end date to yesterday otherwise it will combine today and yesteray
		var toDate = $("#cboPeriod").val() != "yesterday" ? "today" : "yesterday";
		
		byTutorialAnalytics.getReportQuery(id, $("#cboPeriod").val(), toDate, expressions).then(function(response){
			var formattedJson = JSON.stringify(response.result, null, 2);
			$('#json-result').html(formattedJson);
			$("#result").removeClass("hidden").show();
			
			//show result in table
			if(response.result.reports.length > 0){
				var sb = "<table cellpadding='0' cellspacing='0' class='tbl'>";
				for(var i = 0; i < response.result.reports[0].columnHeader.metricHeader.metricHeaderEntries.length; i++){

					if (typeof response.result.reports[0].data.rows !== 'undefined' && response.result.reports[0].data.rows.length > 0) {

						sb += "<tr>";
						sb += "<td>" + response.result.reports[0].columnHeader.metricHeader.metricHeaderEntries[i].name + "</td>";
						sb += "<td>" + response.result.reports[0].data.rows[0].metrics[0].values[i] + "</td>";
						sb += "</tr>";

					}
				}
				sb += "</table>";
				$("#query-result").html(sb);
			}
			byTutorialAnalytics.hideLoading();
		});
	},

	reset: function(){
		$(".result-box").html("");
		$("#result").hide();
	},
	
	//get the analytics query reports, we are using version 4.0
	getReportQuery: function (id, sDate, eDate, dataMetrics){
		return new Promise(function(resolve, reject){
			gapi.client.request({
			  path: '/v4/reports:batchGet',
			  root: 'https://analyticsreporting.googleapis.com/',
			  method: 'POST',
			  body: {
				reportRequests: [
				  {
					viewId: id,
					dateRanges: [
					  {
						startDate: sDate,
						endDate: eDate
					  }
					],
					metrics: dataMetrics
				  }
				]
			  }
			}).then(function(response){
				if(response != null){
					resolve(response);
				}else{
					reject(Error("Error getting the data"));
				}
			})
		});
	},

	//show the transparent waiting progress wrapper
	showLoading: function (){
		$("#transparent-wrapper").show();
	},
	
	//hide the transparent waiting progress wrapper
	hideLoading: function(){
		$("#transparent-wrapper").hide();
	}
}
