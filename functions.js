// Called upon a submission of a login attempt in login.php
function validateLogin(){
  // Assert username and password fields are not empty
  const username = document.getElementById("username").value;
  const password = document.getElementById("password").value;
  var validForm = true;

  if(!username){
    validForm = false;
    document.getElementById("userErr").innerText = "Username cannot be empty";
  }
  else{
    document.getElementById("userErr").innerText = "";
  }

  if(!password){
    validForm = false;
    document.getElementById("pwordErr").innerText = "Password cannot be empty";
  }
  else{
    document.getElementById("pwordErr").innerText = "";  
  }

  return validForm;
}

// Called upon a submission of a user creation attempt in login.php
function validateCreate(){
  // Assert each text input type is not empty, and follows its' character pattern
  const username = document.getElementById("newUsername").value;
  const password = document.getElementById("newPassword").value;
  const name = document.getElementById("fullName").value;
  const state = document.getElementById("userState").value;
  const city = document.getElementById("userCity").value;
  const address = document.getElementById("address").value;
  const payment = document.getElementById("credit").value;
  let validForm = true;

  // Initialize elements for text variables, error block IDs, error message "titles", and character validation expressions 
  const textArr = [username, password, name, state, city, address, payment];
  const idArr = ["unameErr", "pwErr", "fnameErr", "stateErr", "cityErr", "addErr", "payErr"];
  const nameArr = ["Username", "Password", "Name", "State", "City", "Address", "Payment"];
  const validCharArr = [/^[\s\S]*$/, /^[\s\S]*$/, /^[a-zA-Z\s.\'-]+$/, /^[a-zA-Z\s]+$/, /^[a-zA-Z\s.\'-]+$/,  /^[a-zA-Z0-9\s.\'-]+$/, /^[0-9\-]+$/];

  for(let i = 0; i < textArr.length; i++){
    if(!textArr[i]){
      validForm = false;
      document.getElementById(idArr[i]).innerText = nameArr[i] + " cannot be empty";
    }
    else if(!validCharArr[i].test(textArr[i])){
      validForm = false;
      document.getElementById(idArr[i]).innerText = "Invalid " + nameArr[i].toLowerCase() + " format";
    }
    else{
      document.getElementById(idArr[i]).innerText = "";
    }
  }

  return validForm;
}

// Called upon adding an item to the cart in item.php
function validateNumber(){
  // Assert number input type is not empty, and greater than 0
  var quantity = document.getElementById("item").value;
  if(!quantity || quantity <= 0){
    document.getElementById("numErr").innerText = "Quantity must be a positive integer";
    return false;
  }

  return true;
}

// Called upon placing an order in placeOrder.html
function validateOrder(event){
  // Use event parameter to assert the submission was done by the "Place Order" button 
  const submittedButton = event.submitter;
  if(submittedButton.name != "confirm"){
    return true;
  }

  // Assert each text input type is not empty, and follows its' character specifications
  const name = document.getElementById("confName").value;
  const state = document.getElementById("confState").value;
  const city = document.getElementById("confCity").value;
  const address = document.getElementById("confAddress").value;
  const payment = document.getElementById("confCredit").value;
  let validForm = true;

  const textArr = [name, state, city, address, payment];
  const idArr = ["confNameErr", "confStateErr", "confCityErr", "confAddErr", "confCredErr"];
  const nameArr = ["Name", "State", "City", "Address", "Payment"];
  const validCharArr = [/^[a-zA-Z\s.\'-]+$/, /^[a-zA-Z\s]+$/, /^[a-zA-Z\s.\'-]+$/,  /^[a-zA-Z0-9\s.\'-]+$/, /^[0-9\-]+$/];

  for(let i = 0; i < textArr.length; i++){
    if(!textArr[i]){
      validForm = false;
      document.getElementById(idArr[i]).innerText = nameArr[i] + " cannot be empty";
    }
    else if(!validCharArr[i].test(textArr[i])){
      validForm = false;
      document.getElementById(idArr[i]).innerText = "Invalid " + nameArr[i].toLowerCase() + " format";
    }
    else{
      document.getElementById(idArr[i]).innerText = "";
    }
  }

  return validForm;
}

// Called upon a submission of a user "application" in admissions.html
function validateApplication(){
  // Assert all input types are not empty, and that the text types follow correct patterns
  const name = document.getElementById("name").value;
  const city = document.getElementById("city").value;
  const school = document.getElementsByName("school"); //List of radio buttons
  const major = document.getElementById("major").value;
  let validForm = true; //Switch to ensure all form elements are valid
  let buttonSelected = false;

  const isValid = /^[a-zA-Z\s.\'-]+$/;
  if(!name){
    validForm = false;
    document.getElementById("nameErr").innerText = "Name cannot be empty";
  }
  else if(!isValid.test(name)){
    validForm = false;
    document.getElementById("nameErr").innerText = "Invalid name format";
  }
  else{
    document.getElementById("nameErr").innerText = "";  
  }

  if(!city){
    validForm = false;
    document.getElementById("liveErr").innerText = "City cannot be empty";
  }
  else if(!isValid.test(city)){
    validForm = false;
    document.getElementById("liveErr").innerText = "Invalid city format";
  }
  else{
    document.getElementById("liveErr").innerText = "";  
  }

  const isAlpha = /^[a-zA-Z\s]+$/;
  if(!major){
    validForm = false;
    document.getElementById("majorErr").innerText = "Major cannot be empty";
  }
  else if(!isAlpha.test(major)){
    validForm = false;
    document.getElementById("majorErr").innerText = "Major must include only alphabet and space characters";
  }
  else{
    document.getElementById("majorErr").innerText = "";  
  }

  //Ensure a button has been pressed
  for(let i = 0; i < school.length; i++){
    if(school[i].checked){
      buttonSelected = true;
    }
  }
  if(buttonSelected){
    document.getElementById("schoolErr").innerText = "";
  }
  else{
    validForm = false;
    document.getElementById("schoolErr").innerText = "Must select an option";
  }

  return validForm;
}

// Called upon a submission of a trivia form in trivia.php
function validateTrivia(){
    // Assert all input types are not empty, and that the text types follow correct patterns
  const select = document.getElementById("basketball");
  const names = document.getElementById("names").value;
  const conference = document.getElementById("conference").value;
  const date = document.getElementById("month").value;
  const buttons = document.getElementsByName("extra"); //List of radio buttons
  const checks = document.getElementsByName("locate[]"); //List of checkboxes
  const mascot = document.getElementById("spike").value;
  let validForm = true; //Switch to ensure all form elements are valid
  let buttonSelected = false;
  let boxChecked = false;

  //Ensure default value of select element is not the current selection
  if(select.selectedIndex == 0){ 
    validForm = false;
    document.getElementById("bballErr").innerText = "Must select a stadium name";
  }
  else{
    document.getElementById("bballErr").innerText = "";
  }

  if(!names){
    validForm = false;
    document.getElementById("namesErr").innerText = "Must enter a value";
  }
  else if(names <= 0){
    validForm = false;
    document.getElementById("namesErr").innerText = "Value must be a positive integer";
  }
  else{
    document.getElementById("namesErr").innerText = "";  
  }

  const isAlpha = /^[a-zA-Z\s]+$/;
  if(!conference){
    validForm = false;
    document.getElementById("confErr").innerText = "Conference cannot be empty";
  }
  else if(!isAlpha.test(conference)){
    validForm = false;
    document.getElementById("confErr").innerText = "Conference contains only alphabet and space characters";
  }
  else{
    document.getElementById("confErr").innerText = "";  
  }

  if(!mascot){
    validForm = false;
    document.getElementById("mascErr").innerText = "Mascot name cannot be empty";
  }
  else if(!isAlpha.test(mascot)){
    validForm = false;
    document.getElementById("mascErr").innerText = "Mascot contains only alphabet and space characters";
  }
  else{
    document.getElementById("mascErr").innerText = "";  
  }

  if(!date){
    validForm = false;
    document.getElementById("monthErr").innerText = "Must select a month";
  }
  else{
    document.getElementById("monthErr").innerText = "";
  }

  //Ensure a button has been pressed
  for(let i = 0; i < buttons.length; i++){
    if(buttons[i].checked){
      buttonSelected = true;
    }
  }
  if(buttonSelected){
    document.getElementById("extraErr").innerText = "";
  }
  else{
    validForm = false;
    document.getElementById("extraErr").innerText = "Must select an option";
  }

  // Ensure at least one box has been checked;
  for(let i = 0; i < checks.length; i++){
    if(checks[i].checked){ 
      boxChecked = true;
      break;
    }
  }
  if(boxChecked){
    document.getElementById("locateErr").innerText = "";
  }
  else{
    validForm = false;
    document.getElementById("locateErr").innerText = "Must select at least one option";
  }

  return validForm;
}
