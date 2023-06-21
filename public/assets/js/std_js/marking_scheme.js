function cObj(params) {
    return document.getElementById(params);
}

function valObj(params) {
    return document.getElementById(params).value;
}

function grayBorder(object) {
    object.style.borderColor = 'gray';
}
function redBorder(object) {
    object.style.borderColor = 'red';
}
function checkBlank(id){
    let err = 0;
    if(cObj(id).value.trim().length>0){
        if (cObj(id).value.trim()=='N/A') {
            redBorder(cObj(id));
            err++;
        }else{
            grayBorder(cObj(id));
        }
    }else{
        redBorder(cObj(id));
        err++;
    }
    return err;
}
function hasJsonStructure(str) {
    if (typeof str !== 'string') return false;
    try {
        const result = JSON.parse(str);
        const type = Object.prototype.toString.call(result);
        return type === '[object Object]' 
            || type === '[object Array]';
    } catch (err) {
        return false;
    }
}

// save the button
window.onload = function () {
    var review_btn = document.getElementsByClassName("review_btn");
    for (let index = 0; index < review_btn.length; index++) {
        const element = review_btn[index];
        element.addEventListener("click",save_review);
    }

    // show images
    var window_locale = document.getElementsByClassName("window_locale");
    for (let index = 0; index < window_locale.length; index++) {
        const element = window_locale[index];
        element.addEventListener("click",showImages);
    }
}

function showImages() {
    cObj("image_assignments").src = this.src;
}

function save_review() {
    var this_id = this.id.substr(11);
    console.log(this_id);

    var err = 0;
    // err+=checkBlank("comment_here"+this_id);
    err+=checkBlank("marks_attained_"+this_id);

    // check for errors
    var max_value = valObj("max_value_"+this_id);

    if (valObj("marks_attained_"+this_id)*1 > max_value*1) {
        err++;
        cObj("danger_data").innerText = "The maximum marks set is "+max_value+" Mks";
    }else{
        cObj("danger_data").innerText = "";
    }

    if (err == 0) {
        // proceed and get the student data and reviews
        var answers = valObj("my_students_answers");
        if (hasJsonStructure(answers)) {
            answers = JSON.parse(answers);
    
            // reviews
            var present = 0;
            if (hasJsonStructure(answers.answer)) {
                present = 1;
                var my_answers = JSON.parse(answers.answer);
                var my_selected_answers = [];
                for (let index = 0; index < my_answers.length; index++) {
                    const element = my_answers[index];
                    if (element.linked == this_id) {
                        my_selected_answers = element;
                        element.score = valObj("marks_attained_"+this_id);
                        element.review = valObj("comment_here"+this_id);
                    }
                }
                // console.log(my_answers);

                // set answers
                answers.answer = my_answers;
                cObj("my_students_answers").value = JSON.stringify(answers);

                // set spinners
                cObj("spinners_load_"+this_id).classList.remove("hide");
                setTimeout(() => {
                    cObj("spinners_load_"+this_id).classList.add("hide");
                }, 1000);
            }
            
            // if its json
            if (Array.isArray(answers.answer) && present == 0) {
                var my_answers = answers.answer;
                var my_selected_answers = [];
                for (let index = 0; index < my_answers.length; index++) {
                    const element = my_answers[index];
                    if (element.linked == this_id) {
                        my_selected_answers = element;
                        element.score = valObj("marks_attained_"+this_id);
                        element.review = valObj("comment_here"+this_id);
                    }
                }
                // console.log(my_answers);

                // set answers
                answers.answer = my_answers;
                cObj("my_students_answers").value = JSON.stringify(answers);

                // set spinners
                cObj("spinners_load_"+this_id).classList.remove("hide");
                setTimeout(() => {
                    cObj("spinners_load_"+this_id).classList.add("hide");
                }, 1000);
            }
        }
    }
}
