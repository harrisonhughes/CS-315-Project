// Allows the code to being when HTML elements are all loaded
document.addEventListener('DOMContentLoaded', function() { 
  //Holds form block if loaded specific form type otherwise will be null
  const academicForm = document.querySelector("#schoolForm");
  const triviaForm = document.querySelector("#triviaForm"); 

  if(triviaForm == null){ // Execute this section if on "Academic" page
    const schoolEntry = document.getElementById("schoolJSON"); // Load and post last form entry if there is one
    schoolEntry.innerHTML = localStorage.getItem("schoolJSON");

    //Function will execute each time submit button is pressed
    academicForm.addEventListener("submit", function (e) {
      const name = document.querySelector("#name");
      const city = document.querySelector("#city");
      const major = document.querySelector("#major");
      const textArray = [name, city, major]; //All text input blocks placed into an array
      const buttons = document.getElementsByName("school"); //List of all radio buttons titled "school"
      let buttonSelected = false; //Switch to ensure a radio button has been pressed
      let validForm = true; //Switch to ensure all form elements are valid
  
      for(let text of textArray){
        if(!text.value){ //Ensure all text input types are valid (not empty)
          text.nextElementSibling.classList.add("invalid"); //"invalid" class displays error message
          text.nextElementSibling.classList.remove("valid"); //"valid" class hides error message
          validForm = false;
        } 
        else{ //Otherwise, this specific text element does have a valid response
          text.nextElementSibling.classList.add("valid");
          text.nextElementSibling.classList.remove("invalid");
        }
      }
  
      //Ensure a button has been pressed, save it under "school" variable if true
      for(let i = 0; i < buttons.length; i++){
        if(buttons[i].checked){
          buttonSelected = true;
          var school = buttons[i].nextElementSibling.textContent;
          break;
        }
      }

      if(buttonSelected == false){ //If no buttons selected, execute invalid element procedures
        document.querySelector("#buttons~p").classList.add("invalid");
        document.querySelector("#buttons~p").classList.remove("valid");
        validForm = false;
      }
      else{ //Otherwise, a button has indeed been pressed
        document.querySelector("#buttons~p").classList.add("valid");
        document.querySelector("#buttons~p").classList.remove("invalid");
      }
  
      if(!validForm){ //Prevent submission if any form elements are invalid
        e.preventDefault();
        return;
      }
      else{// Otherwise, build JSON and set to storage
        let select = document.getElementById("state"); 
        let state = select.options[select.selectedIndex].text; //Find selected item
        let rating = document.getElementById("rate").value; //Store range value
  
        let formInfo = {Name:name.value, City:city.value, State:state, School:school, Major:major.value, Rating:rating};
        let output = JSON.stringify(formInfo);
        localStorage.setItem("schoolJSON", output);
      }
    });
  }
  else{ //Otherwise, triviaForm is not null, implying we are currently on the "Trivia" HTML page
    const triviaEntry = document.getElementById("triviaJSON"); //Load and post form entry if there is one
    triviaEntry.innerHTML = localStorage.getItem("triviaJSON");

    //Execute following funciton every time "submit" button is pressed
    triviaForm.addEventListener("submit", function (e) {
      const select = document.querySelector("#basketball");
      const names = document.querySelector("#names");
      const conference = document.querySelector("#conference");
      const date = document.querySelector("#month");
      const buttons = document.getElementsByName("extra"); //List of radio buttons
      const checks = document.getElementsByName("locate"); //List of checkboxes
      const mascot = document.querySelector("#spike");
      const textArray = [names, conference, mascot, date]; //Holds all text input types (including number and month)
      let buttonSelected = false; //Switch to ensure a radio button has been selected
      let boxChecked = false; //Switch to ensure at least one box has been checked
      let validForm = true; //Switch to ensure all form elements are valid

      if(select.selectedIndex == 0){ //Ensure default value of select element is not the current selection
        select.nextElementSibling.classList.add("invalid"); //"invalid" class displays error message
        select.nextElementSibling.classList.remove("valid"); //"valid" class hides error message
        validForm = false;
      }
      else{//Otherwise, an option has been selected
        select.nextElementSibling.classList.add("valid"); 
        select.nextElementSibling.classList.remove("invalid");
      }

      for(let text of textArray){ //Ensure all text input types are valid (not empty, or 0 for number)
        if(!text.value){
          text.nextElementSibling.classList.add("invalid");
          text.nextElementSibling.classList.remove("valid");
          validForm = false;
        } 
        else{ //Otherwise, current element has valid response
          text.nextElementSibling.classList.add("valid");
          text.nextElementSibling.classList.remove("invalid");
        }
      }

      //Ensure a button has been pressed, save it under "school" variable if true
      for(let i = 0; i < buttons.length; i++){
        if(buttons[i].checked){
          buttonSelected = true;
          var school = buttons[i].nextElementSibling.textContent;
          break;
        }
      }


      var direction = []; //Initialize array to hold all checkbox responses
      for(let i = 0; i < checks.length; i++){
        if(checks[i].checked){ //Save all responses, if none are selected, boxChecked switch will remain "false"
          boxChecked = true;
          direction.push(checks[i].nextElementSibling.textContent);
        }
      }

      if(buttonSelected == false){ //Invalid if no radio buttons selected
        document.querySelector("#buttons~p").classList.add("invalid");
        document.querySelector("#buttons~p").classList.remove("valid");
        validForm = false;
      }
      else{ //Otherwise, a button has indeed been pressed
        document.querySelector("#buttons~p").classList.add("valid");
        document.querySelector("#buttons~p").classList.remove("invalid");
      }

      if(boxChecked == false){ //Invalid if no checkboxes selected
        document.querySelector("#checks~p").classList.add("invalid");
        document.querySelector("#checks~p").classList.remove("valid");
        validForm = false;
      }
      else{ //Otherwise, at least one box has been checked
        document.querySelector("#checks~p").classList.add("valid");
        document.querySelector("#checks~p").classList.remove("invalid");
      }

      if(!validForm){ //Prevent submission if any form elements are invalid
        e.preventDefault();
        return;
      }
      else{ //Otherwise, build JSON and set to storage
        const arena = select.options[select.selectedIndex].text; //Find selected option from select box
        const month = date.value.slice(5, 8); //Slice selected month from month input type (YYYY-MM)->(MM)
  
        let formInfo = {Arena:arena, Names:names.value, Conference:conference.value, Month:month, School:school, Direction:direction, Mascot:mascot.value};
        let output = JSON.stringify(formInfo);
        localStorage.setItem("triviaJSON", output);
      }
    });
  }
});