//changes modal form's "action" attribute according to the element clicked and redirects to the designated code
function categRate(id, category) { document.forms.modalCateg.action = "mymovies.php?id="+id+"&url=categories.php?category="+category.value; }
function listsRate(id, string) { document.forms.modalList.action = "mymovies.php?id="+id+"&url=watchlist.php#"+string; document.getElementById('close-x').href="#"+string;}
function ranksRate(id, string) { document.forms.modalRank.action = "mymovies.php?id="+id+"&url=ranking.php#"+string; document.getElementById('close-x').href="#"+string;}
function resultsRate(id) { document.forms.modalResult.action = "mymovies.php?id="+id+"&url=results.php"; }
function homeRate(id, string) { document.forms.modalHome.action = "mymovies.php?id="+id+"&url=home.php#"+string; }

//changes modal label and adds a "delete" button when there exists a rating the user logged in gave to a movie
function modalDetails(rating) { 
    document.getElementById("modal-h").innerHTML = "Would you like to change your rating?"; 
    document.getElementById("r"+rating).checked = true;
    let container = document.getElementById("del-appear");
    let input = document.createElement('input');
    input.type = "submit";
    input.value = "Delete";
    input.id = "del-appear-btn";
    input.name = "rate_del";
    container.appendChild(input);
    document.getElementById('del-appear-btn').classList.add("modal-footer_btn");
    document.getElementById('del-appear-btn').classList.add("delete");
}

//removes the modal's delete button and label as to reset it to its original state
function delDisappear() {
    document.getElementById("modal-h").innerHTML = "How would you rate this movie?"; 
    var element = document.getElementById('del-appear-btn');
    if (typeof(element) != 'undefined' && element != null) { element.remove(); }
}

//display or hash password in user's personal information page
function displayPwd() {
    document.getElementById("pwd-bullet").classList.toggle("hidden");
    document.getElementById("pwd-show").classList.toggle("hidden");
    let icon = document.getElementById("icon");
    icon.classList.remove("fa-eye-slash");
    icon.classList.add("fa-eye");
}

function hidePwd() {
    document.getElementById("pwd-bullet").classList.toggle("hidden");
    document.getElementById("pwd-show").classList.toggle("hidden");
    let icon = document.getElementById("icon");
    icon.classList.remove("fa-eye");
    icon.classList.add("fa-eye-slash");
}





