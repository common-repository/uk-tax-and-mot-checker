//RapidCarCheck Tax & MOT Checker WordPress Plugin
document.getElementById("RegSearchBox")
    .addEventListener("keyup", function(event) {
    event.preventDefault();
    if (event.keyCode === 13) {
        document.getElementById("Reg").click();
    }
});

function checkthestatus () {

//loading results	
document.getElementById("content123").innerHTML = my_options.loadinghtm;	

CurrentVrm = document.getElementById("RegSearchBox").value;

var data = {
action: 'uk_mot_tax_checker_jsoncall',
Reg: CurrentVrm
};

jQuery.post(my_options.ajaxurl, data, function(response) {
document.getElementById("content123").innerHTML = response;
});

}