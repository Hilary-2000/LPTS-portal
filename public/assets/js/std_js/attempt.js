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

window.onload = function () {
    displayQuestion(0);
}

cObj("save_answers").onclick = function () {
    var your_answer = valObj("your_answer");
    var assignment_answers = valObj("assignment_answers");
    var quiz_id = cObj("quiz_id").value;

    // parse data
    if (hasJsonStructure(assignment_answers)) {
        // parse answers
        assignment_answers = JSON.parse(assignment_answers);

        // loop to get the id of the latest question
        var id = 0;
        var present = 0;
        for (let index = 0; index < assignment_answers.length; index++) {
            const element = assignment_answers[index];
            if (element.id > id) {
                id = element.id;
            }

            // check if the id of the question is present
            // if it is replace the amswers
            if (element.linked == quiz_id) {
                element.answer = your_answer;
                present = 1;
            }

        }

        // get the data here
        if (present == 0) {
            // add the new question
            id+=1;

            // set the answers
            var questions = {id:id,answer:your_answer,linked:quiz_id};
            assignment_answers.push(questions);
        }

        // set the questions to the answer holder
        cObj("assignment_answers").value = JSON.stringify(assignment_answers);

        // show saved status 
        cObj("saved_status").classList.remove("hide");
        setTimeout(() => {
            cObj("saved_status").classList.add("hide");
        }, 1000);
    }else{
        var questions = [{id:id,answer:your_answer,linked:quiz_id}];
        cObj("assignment_answers").value = JSON.stringify(questions);
    }
}

function displayQuestion(index) {
    var questions = valObj("question_data_input");
    if (hasJsonStructure(questions)) {
        questions = JSON.parse(questions);

        // get the question index
        if (index >= questions.length) {
            index = 0;
            total_index = 0;
        }
        if (index < 0) {
            index = 0;
            total_index = index;
        }

        // get the data
        // console.log(questions);

        // set the data

        // question
        cObj("question_displayer").innerHTML = questions[index].quiz;

        // set marks
        cObj("question_marks").innerHTML = questions[index].points+" Mks";

        // set quiz id
        cObj("quiz_id").value = questions[index].id;

        // set question number
        cObj("question_number").innerText = "Q"+(index+1);

        // display images
        var images = questions[index].resources;
        if (hasJsonStructure(images)) {
            images = JSON.parse(images);

            var data_to_display = "";
            for (let index = 0; index < images.length; index++) {
                const element = images[index];
                data_to_display+=""
                +"<div class='mx-1 my-1' style='width: 100px; cursor:pointer;' data-bs-toggle='modal' data-bs-target='#ExtralargeModal'>"+
                "<img src='"+element.locale+"' id='window_locale"+element.id+"' class='window_locale my-1 mx-auto' alt='' width='90' height='90'>"+
                "<span class='text-center' id='figure_name"+element.id+"'>"+element.name+"</span></div>";
            }
            cObj("data_to_display").innerHTML = data_to_display;

            // set the listener
            var window_locale = document.getElementsByClassName("window_locale");
            for (let index = 0; index < window_locale.length; index++) {
                const element = window_locale[index];
                element.addEventListener("click",showImage);
            }
        }

        // set the progress bar
        var status = ((total_index+1) / questions.length )* 100;
        // console.log(status);
        if ((total_index+1) == questions.length) {
            cObj("save_n_next").classList.add("hide");
            cObj("back_to_question").classList.remove("hide");

            cObj("submit_assignments").classList.remove("hide");
        }else{
            cObj("save_n_next").classList.remove("hide");
            cObj("submit_assignments").classList.add("hide");
        }

        if((total_index+1) == 1){
            cObj("save_n_next").classList.remove("hide");
            cObj("back_to_question").classList.add("hide");
        }else{
            cObj("back_to_question").classList.remove("hide");
        }

        // display multiple choices
        var choices = questions[index].choice;
        if (hasJsonStructure(choices)) {
            choices = JSON.parse(choices);

            // select choices
            var counted = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z']
            var data_to_display = "";
            if (choices.length > 0) {
                data_to_display += "<label class='form-label'><b>Multiple Choices</b></label>";
            }
            for (let index = 0; index < choices.length; index++) {
                const element = choices[index];
                data_to_display+="<p class='my-1 text-secondary'><b>"+counted[index]+".</b> "+element.choice+"</p>";
            }

            // display
            cObj("multiple_choices").innerHTML = data_to_display;
        }
        
        cObj("progress_bars").style.width = Math.round(status)+"%";

        // function to set the answer for a particular question
        var assignment_answers = cObj("assignment_answers").value;
        var present = 0;
        if (hasJsonStructure(assignment_answers)) {
            assignment_answers = JSON.parse(assignment_answers);
            for (let index = 0; index < assignment_answers.length; index++) {
                const element = assignment_answers[index];
                if (element.linked == cObj("quiz_id").value) {
                    cObj("your_answer").value = element.answer;
                    present = 1;
                    break;
                }
            }
        }

        // is present 
        if (present == 0) {
            cObj("your_answer").value = "";
        }
    }
}

function showImage() {
    // image src
    var this_src = this.src;

    // image assignments
    cObj("image_assignments").src = this_src;

    // set the image title
    cObj("title_image").innerText = cObj("figure_name"+this.id.substr(13)).innerText;
}
var total_index = 0;

cObj("save_n_next").onclick  = function () {
    var quiz_id = valObj("quiz_id");
    total_index ++;
    displayQuestion(total_index);

    // save the answer for that question
}

cObj("back_to_question").onclick = function () {
    total_index --;
    displayQuestion(total_index);
}