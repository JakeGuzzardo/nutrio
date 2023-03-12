
//set these to adjust goals in grams
let protienGoalGrams = 160
let carbGoalGrams = 170
let fatGoalGrams = 60

//converts grams to calories
let protienGoalCals = protienGoalGrams * 4
let carbGoalCals = carbGoalGrams * 4
let fatGoalCals = fatGoalGrams * 9

let calorieGoal = protienGoalCals + carbGoalCals + fatGoalCals //1860

//values for testing in grams
let currentProtien = 107
let currentCarb = 78
let currentFat = 21

//values in calories
let curPro_K = currentProtien * 4
let curCarb_K = currentCarb * 4
let curFat_K = currentFat * 9

let userId = 69;

getCalorieGoals()

let totalCur_K = curPro_K + curCarb_K + curFat_K

let protienProgress = document.querySelector(".protienProgress"),
    protienValue = document.querySelector("#protien");

protienValue.innerHTML = `${currentProtien} / ${protienGoalGrams} grams`
protienProgress.style.background = `conic-gradient(rgb(196, 32, 60) ${(currentProtien/protienGoalGrams) * 360}deg, #ededed 0deg)`

let carbProgress = document.querySelector(".carbsProgress"),
    carbValue = document.querySelector("#carbs");

    carbValue.innerHTML = `${currentCarb} / ${carbGoalGrams} grams`
    carbProgress.style.background = `conic-gradient(rgb(33, 157, 205) ${(currentCarb/carbGoalGrams) * 360}deg, #ededed 0deg)`

let fatProgress = document.querySelector(".fatsProgress"),
    fatValue = document.querySelector("#fats");

    fatValue.innerHTML = `${currentFat} / ${fatGoalGrams} grams`
    fatProgress.style.background = `conic-gradient(rgb(230, 227, 71) ${(currentFat/fatGoalGrams) * 360}deg, #ededed 0deg)`

let cals = document.querySelector('#cals')

cals.innerHTML = `${totalCur_K}/${calorieGoal}`




function getCalorieGoals(user_id, div_id) {
    let xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function(){
    if(this.readyState === 4 && this.status === 200)
      {
        document.getElementById(div_id).innerHTML = this.responseText
      }
    }
	//get request with params
    xhr.open('GET', '/CSE442-542/2023-Spring/cse-442g/project_s23-the-ai-violators/public/signup/getCals.php?user_id=' + user_id);
  xhr.send();
}