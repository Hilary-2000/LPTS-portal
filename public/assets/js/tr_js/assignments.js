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
function checkBlank(id) {
    let err = 0;
    if (cObj(id).value.trim().length > 0) {
        if (cObj(id).value.trim() == 'N/A') {
            redBorder(cObj(id));
            err++;
        } else {
            grayBorder(cObj(id));
        }
    } else {
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
    var window_locale = document.getElementsByClassName("window_locale");
    for (let index = 0; index < window_locale.length; index++) {
        const element = window_locale[index];
        element.addEventListener("click",displayWindow);
    }
}

function displayWindow() {
    var this_id = this.id.substr(13);
    var this_class = this.className;
    this_class = this_class.split(" ");

    // get the id of the class
    var this_class_id = this_class[1].substr(6);

    // get the data 
    var this_value = cObj("values_id_"+this_id+this_class_id).value;

    // decode the data
    if (hasJsonStructure(this_value)) {
        this_value = JSON.parse(this_value);

        // set the image locale and the title name
        cObj("title_image").innerHTML = this_value.name;

        // set the image location
        cObj("image_assignments").src = this_value.locale;
        cObj("my_ids").value = this_id;

        // add all images
        cObj("all_images").value = cObj("inside_values_"+this_id+this_class_id).value;
    }
}

cObj("move_right_inside").onclick = function () {
    var my_images = cObj("all_images").value;
    if (hasJsonStructure(my_images)) {
        my_images = JSON.parse(my_images);

        // move the data after the index selected
        var my_ids = cObj("my_ids").value;
        if ((my_images.length) == (my_ids*=1)+1) {
            my_ids = 0;
        }else{
            my_ids*=1;
            my_ids++;
        }

        // set the image data
        // set the image locale and the title name
        cObj("title_image").innerHTML = my_images[my_ids].name;

        // set the image location
        cObj("image_assignments").src = my_images[my_ids].locale;
        cObj("my_ids").value = my_ids;
    }
}

cObj("move_left_inside").onclick = function () {
    var my_images = cObj("all_images").value;
    if (hasJsonStructure(my_images)) {
        my_images = JSON.parse(my_images);

        // move the data after the index selected
        var my_ids = cObj("my_ids").value;
        if ((my_ids*=1) <= 0) {
            my_ids = (my_images.length - 1);
        }else{
            my_ids*=1;
            my_ids--;
        }

        // set the image data
        // set the image locale and the title name
        cObj("title_image").innerHTML = my_images[my_ids].name;

        // set the image location
        cObj("image_assignments").src = my_images[my_ids].locale;
        cObj("my_ids").value = my_ids;
    }
}
cObj("add_multiple_choices").onclick = function () {
    var err = checkBlank("multiple_choices");
    if (err == 0) {
        // data
        var multiple_choices = valObj("multiple_choices");
        // multiple choice holder
        var multiple_choices_holder = valObj("multiple_choices_holder");
        if (hasJsonStructure(multiple_choices_holder)) {
            // multiple choice holder
            multiple_choices_holder = JSON.parse(multiple_choices_holder);
    
            var id = 0;
            for (let index = 0; index < multiple_choices_holder.length; index++) {
                const element = multiple_choices_holder[index];
                if(element.id >= id){
                    id = element.id;
                }
            }
    
            // id
            id+=1;
    
            // choice object
            var choice_obj = {id:id,choice:multiple_choices};
            multiple_choices_holder.push(choice_obj);
    
            // stringify and save
            cObj("multiple_choices_holder").value = JSON.stringify(multiple_choices_holder);
        }else{
            var choice_obj = [{id:1,choice:multiple_choices}];
            cObj("multiple_choices_holder").value = JSON.stringify(choice_obj);
        }
        cObj("multiple_choices").value = "";
        displayChoices();
    }
}

function displayChoices() {
    var multiple_choices_holder = valObj("multiple_choices_holder");

    if (hasJsonStructure(multiple_choices_holder)) {
        multiple_choices_holder = JSON.parse(multiple_choices_holder);
        if (multiple_choices_holder.length > 0) {
            var data_to_display = "<ul class='list-group'>";
            for (let index = 0; index < multiple_choices_holder.length; index++) {
                const element = multiple_choices_holder[index];
                data_to_display+="<li class='list-group-item'>"+(index+1)+". "+element.choice+" <span class='multiple_choice text-danger' id='multiple_choice"+element.id+"'> <i class='bi bi-trash' style='cursor:pointer;'></i></span></li>";
            }
            data_to_display+="</ul>";
    
            // display the data
            cObj("display_choices_window").innerHTML = data_to_display;
        }else{
            cObj("display_choices_window").innerHTML = "<ul class='list-group'><h3 class='text-center text-secondary mt-1'><iclass='bi bi-exclamation-triangle'></i></h3><p class='text-secondary text-center'>No Choices set!</p></ul>";
        }
    }else{
        cObj("display_choices_window").innerHTML = "<ul class='list-group'><h3 class='text-center text-secondary mt-1'><iclass='bi bi-exclamation-triangle'></i></h3><p class='text-secondary text-center'>No Choices set!</p></ul>";
    }

    // set listerner
    var multiple_choice = document.getElementsByClassName("multiple_choice");
    for (let index = 0; index < multiple_choice.length; index++) {
        const element = multiple_choice[index];
        element.addEventListener("click", delMultipleChoice);
    }
}

function delMultipleChoice() {
    var this_id = this.id.substr(15);

    // delete the data selected
    var multiple_choices_holder = valObj("multiple_choices_holder");
    if (hasJsonStructure(multiple_choices_holder)) {
        multiple_choices_holder = JSON.parse(multiple_choices_holder);
        var new_data = [];
        for (let index = 0; index < multiple_choices_holder.length; index++) {
            const element = multiple_choices_holder[index];
            if (element.id != this_id) {
                new_data.push(element);
            }
        }
        cObj("multiple_choices_holder").value = JSON.stringify(new_data);
    }
    displayChoices();
}

cObj("assignment_question").addEventListener("keyup", (event) => {
    cObj("assignment_question_holder").value = cObj("assignment_question").value;
});
cObj("maximum_points").addEventListener("keyup", (event) => {
    cObj("maximum_points_holder").value = cObj("maximum_points").value;
});

cObj("add_images_btn").onclick = function () {
    var err = checkBlank("resource_name");
    err+=checkBlank("resource_location");
    
    if (err == 0) {
        const xhr = new XMLHttpRequest();
        const form = document.getElementById('form_handlers_inside');
        const fileInput = document.getElementById('resource_location');
        const progressBar = document.getElementById('progress_bars');
        const progressBarText = document.getElementById('progress_bars');
        cObj("file_progress_bars").classList.remove("hide");
    
        xhr.open('POST', '/Assignments/uploadAss');
        xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        xhr.upload.addEventListener('progress', function (e) {
            const percent = e.loaded / e.total * 100;
            progressBar.style.width = percent + '%';
            progressBar.setAttribute('aria-valuenow', percent);
            progressBarText.innerText = Math.round(percent) + '%';
    
            if (Math.round(percent) == 100) {
                cObj("text_holders_in").innerHTML = "<p class='text-success'>File is processing. Please wait!</p>";
            }
        });
        xhr.addEventListener('load', function (e) {
            progressBar.style.width = '100%';
            progressBar.setAttribute('aria-valuenow', 100);
            progressBarText.innerText = '100%';
            
            setTimeout(() => {
                cObj("file_progress_bars").classList.add("hide");
            }, 1000);
            cObj("text_holders_in").innerHTML = "<p class='text-success'>File uploaded successfully!</p>";
            setTimeout(() => {
                cObj("text_holders_in").innerHTML = "";
            }, 3000);

            
            var response = this.response;
            if (hasJsonStructure(response)) {
                // response
                response = JSON.parse(response);

                var data = {id:1,locale:response[2]+"/"+response[1],name:response[0]};
                
                var resources_location = valObj("resources_location");
                if (hasJsonStructure(resources_location)) {
                    resources_location = JSON.parse(resources_location);

                    // get id of this new resource
                    var id = 0;
                    for (let index = 0; index < resources_location.length; index++) {
                        const element = resources_location[index];
                        if (element.id >= id) {
                            id = element.id;
                        }
                    }

                    // add the id
                    id += 1;
                    data.id = id;

                    // add it to the list
                    resources_location.push(data);

                    // save it to the list
                    cObj("resources_location").value = JSON.stringify(resources_location);
                }else{
                    var infor = [data];
                    cObj("resources_location").value = JSON.stringify(infor);
                }
                cObj("resource_name").value = "";
                cObj("resource_location").value = "";
            }
            displayResources();
        });
        xhr.addEventListener('error', function (e) {
            console.log('An error occurred while uploading the file.');
        });
    
        const formData = new FormData();
        formData.append('file', fileInput.files[0]);
        formData.append('resource_name', valObj("resource_name"));
        xhr.send(formData);
    }else{

    }
}

function displayResources() {
    var resources_location = valObj("resources_location");
    if (hasJsonStructure(resources_location)) {
        // parse value
        resources_location = JSON.parse(resources_location);

        if (resources_location.length > 0) {
            var data_to_display = "";
            for (let index = 0; index < resources_location.length; index++) {
                const element = resources_location[index];
                data_to_display+="<div class='mx-1 my-1' style='width: 100px;'><img src='"+element.locale+"' class='my-1 mx-auto'alt='' width='90' height='90'><span class='text-center'>"+element.name+" <span class='text-danger delete_image' id='delete_image"+element.id+"' style='cursor: pointer;'><i class='bi bi-trash'></i></span></span></div>";
            }
    
            cObj("resource_display").innerHTML = data_to_display;
    
            var delete_image = document.getElementsByClassName("delete_image");
            for (let index = 0; index < delete_image.length; index++) {
                const element = delete_image[index];
                element.addEventListener("click",deleteResource);
            }
        }else{
            cObj("resource_display").innerHTML = "<h3 class='text-center text-secondary mt-1'><iclass='bi bi-exclamation-triangle'></i></h3><p class='text-secondary text-center'>No Resources added yet!</p>";
        }
    }else{
        cObj("resource_display").innerHTML = "<h3 class='text-center text-secondary mt-1'><iclass='bi bi-exclamation-triangle'></i></h3><p class='text-secondary text-center'>No Resources added yet!</p>";
    }
}

function deleteResource() {
    var this_id = this.id.substr(12);
    var resources_location = valObj("resources_location");
    if (hasJsonStructure(resources_location)){
        resources_location = JSON.parse(resources_location);
        var new_data = [];
        var data_send = [];

        for (let index = 0; index < resources_location.length; index++) {
            const element = resources_location[index];
            if (element.id != this_id) {
                new_data.push(element);
            }else{
                data_send.push(element);
            }
        }
        cObj("resources_location").value = JSON.stringify(new_data);
        displayResources();

        // check if the data has been found and addes
        if (data_send.length > 0) {
            var location = data_send[0].locale;
            // send the location to delete the file
            const xhr = new XMLHttpRequest();
            const progressBar = document.getElementById('progress_bars');
            const progressBarText = document.getElementById('progress_bars');
            xhr.open('POST', '/Assignments/deleteAssResources');
            xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            xhr.upload.addEventListener('progress', function (e) {
                const percent = e.loaded / e.total * 100;
                progressBar.style.width = percent + '%';
                progressBar.setAttribute('aria-valuenow', percent);
                progressBarText.innerText = Math.round(percent) + '%';
        
                if (Math.round(percent) == 100) {
                    cObj("text_holders_in").innerHTML = "<p class='text-success'>File is deleting. Please wait!</p>";
                }
            });
            xhr.addEventListener('load', function (e) {
                progressBar.style.width = '100%';
                progressBar.setAttribute('aria-valuenow', 100);
                progressBarText.innerText = '100%';
                
                setTimeout(() => {
                    cObj("file_progress_bars").classList.add("hide");
                }, 1000);
                var response = this.response;
                cObj("text_holders_in").innerHTML = "<p class='text-success'>"+response+"!</p>";
                setTimeout(() => {
                    cObj("text_holders_in").innerHTML = "";
                }, 3000);
            });
            xhr.addEventListener('error', function (e) {
                console.log('An error occurred while deleting the file.');
            });
        
            const formData = new FormData();
            formData.append('resources_location', location);
            xhr.send(formData);
        }
    }
}

cObj("correct_answer").onkeyup = function () {
    var admin_correct_answers = this.value;
    cObj("admin_correct_answers").value = admin_correct_answers;
}