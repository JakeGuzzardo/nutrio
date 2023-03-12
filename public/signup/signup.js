let password = document.getElementById("password")
let confirm = document.getElementById("confirmPassword")
let submit = document.getElementById("submitButton")

let email = document.getElementById("email")
let username = document.getElementById("username")

let form = document.getElementsByTagName("form")[0]

let takenEmail = "Taken@example.com"
let takenUsername = "Taken"


function check(){
    if(password.value != confirm.value){
        confirm.setCustomValidity("Passwords must be matching")
    } else {
        confirm.setCustomValidity('')
    }
}

form.addEventListener("submit", e => {
   e.preventDefault()
   let errorCircle = '<i class="fa fa-times-circle"></i>'
   let error = document.querySelector(".error-msg")
   if(email.value === takenEmail && username.value === takenUsername){
        error.style.display = "block"
        error.innerHTML = `${errorCircle} Email and Username already taken`
    }
    else if(username.value === takenUsername){
        error.style.display = "block"
        error.innerHTML = `${errorCircle} Username already taken`
    
   }
   else if(email.value === takenEmail){
        error.style.display = "block"
        error.innerHTML = `${errorCircle} Email already taken`
   }
   else{
    //submitForm() //This function has no current functionality
    error.style.display = "none"
    let success = document.querySelector(".success-msg")
    success.style.display = "block"
   }

})

