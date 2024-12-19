
function swap(listIdPrefix) { collapsedList = document.getElementById(listIdPrefix + "_collapsed");
expandedList = document.getElementById(listIdPrefix + "_expanded");
if (collapsedList.style.display == "block") {
collapsedList.style.display = "none";
expandedList.style.display = "block";
} else {
collapsedList.style.display = "block";
expandedList.style.display = "none";
}
} 


function externalLinks() {
if (!document.getElementsByTagName) return; 
var anchors = document.getElementsByTagName("a"); 
for (var i=0; i<anchors.length; i++) { 
var anchor = anchors[i]; 
if (anchor.getAttribute("href") && 
anchor.getAttribute("rel") == "external") 
anchor.target = "_blank";
} 
} 
window.onload = externalLinks;

