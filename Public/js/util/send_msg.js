function createRequest(){
		var xmlHttp;
		if (window.XMLHttpRequest){
		    xmlHttp = new XMLHttpRequest();
		}
		else if (window.ActiveXObject)
		{
		    try{
		        xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
		    }
		    catch (e)
		    {
		        try{
		            xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
		        }
		        catch (e) {}
		    }
		}
		return xmlHttp;
	}

function request(xmlHttp,data,url)
{
	xmlHttp.open("POST",url, false);
	xmlHttp.setRequestHeader('Content-type','application/x-www-form-urlencoded');	
	xmlHttp.send(data);

	//alert(xmlHttp.responseText);
}