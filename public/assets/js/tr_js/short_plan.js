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

cObj("main_date_selector").onchange = function () {
    displayData();
}

cObj("plan_resources").onchange = function () {
    var its_value = this.value;
    if (its_value == "Notes/Documents") {
        var windows = document.getElementsByClassName("windows");
        for (let index = 0; index < windows.length; index++) {
            const element = windows[index];
            element.classList.add("hide");
        }
        cObj("select_notes_window").classList.remove("hide");
    } else if (its_value == "Videos") {
        var windows = document.getElementsByClassName("windows");
        for (let index = 0; index < windows.length; index++) {
            const element = windows[index];
            element.classList.add("hide");
        }
        cObj("youtube_video_upload_window").classList.remove("hide");
    } else if (its_value == "Book Reference") {
        var windows = document.getElementsByClassName("windows");
        for (let index = 0; index < windows.length; index++) {
            const element = windows[index];
            element.classList.add("hide");
        }
        cObj("book_refferences_window").classList.remove("hide");
    } else if (its_value == "Videos_ids") {
        var windows = document.getElementsByClassName("windows");
        for (let index = 0; index < windows.length; index++) {
            const element = windows[index];
            element.classList.add("hide");
        }
        cObj("youtube_video_ids_window").classList.remove("hide");
    }
}

window.onbeforeunload = function () {
    var short_term_data_original = cObj("short_term_data_original").value;
    var short_term_data = cObj("short_term_data").value;
    if (short_term_data != short_term_data_original && !bypass_alert) {
        // this means that the data has been manipulated
        var confirmationMessage = 'Your changes won`t be saved, Are you sure you want to leave?';
        if (!window.confirm(confirmationMessage)) {
            // get the selected
            // User clicked "No"
            // e.returnValue = confirmationMessage; // For Safari
            return confirmationMessage; // For other browsers
        }
    }
}

var bypass_alert = false;
cObj("ManageSHortPlan").onsubmit = function () {
    bypass_alert = true;
}

window.onload = function () {
    displayData();
}
function getCurrentDate() {
    const now = new Date();
    const year = now.getFullYear();
    const month = (now.getMonth() + 1).toString().padStart(2, '0');
    const day = now.getDate().toString().padStart(2, '0');
    return `${year}${month}${day}`;
}
function getTermWeek(date_details, date) {
    date = date.split("-");
    var new_date = date.length == 3 ? date[0] + date[1] + date[2] : getCurrentDate();

    // get the term and week of the date
    new_date *= 1;

    var term_dates = ["Not Set", "Not Set"];
    // loopt through the list
    for (let index = 0; index < date_details.length; index++) {
        const element = date_details[index];
        if (new_date >= element.date_start && new_date <= element.date_end) {
            term_dates = [element.term, element.week];
        }
    }

    return term_dates;
}

cObj("display_plans").onclick = function () {
    displayData();
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const options = {
        weekday: 'short',
        month: 'long',
        day: 'numeric',
        year: 'numeric',
        timeZone: 'UTC'
    };
    const formattedDate = date.toLocaleDateString('en-US', options);
    return formattedDate;
}

function displayData() {
    var main_date_selector = valObj("main_date_selector");
    var original_date = main_date_selector;
    if (main_date_selector.length > 0) {
        // get the term and week
        var term_n_week = getTermWeek(dates_details, main_date_selector);

        // set the term and week
        cObj("date_holders").innerText = formatDate(main_date_selector);
        cObj("week_holder").innerText = "Week " + term_n_week[1];
        cObj("term_holder").innerText = term_n_week[0];

        // go ahead and get the data of the daily plan
        var activities = [];
        var objectives = [];
        var resources = [];
        var sub_strand = "Not Set";
        for (let index = 0; index < medium_term_plan.length; index++) {
            const element = medium_term_plan[index];
            if (element.term_name == term_n_week[0] && element.week_name == term_n_week[1]) {
                for (let index = 0; index < element.activities.length; index++) {
                    const elem = element.activities[index];
                    activities.push(elem.value);
                }

                for (let index = 0; index < element.objectives.length; index++) {
                    const elem = element.objectives[index];
                    objectives.push(elem.value);
                }

                for (let index = 0; index < element.resources.length; index++) {
                    const elem = element.resources[index];
                    resources.push(elem.value);
                }

                sub_strand = element.sub_strand == undefined ? "Not Set" : element.sub_strand;
            }
        }

        // set the innertext with the data
        cObj("sub_strand_topics_assoc").innerText = sub_strand;

        // get the topic
        var week_number  = 0;
        var topic_set = "Not Set";
        for (let index = 0; index < longterm_plan_data.length; index++) {
            const element = longterm_plan_data[index];
            if (element.term == term_n_week[0].split(" ")[1]) {
                week_number+=element.period;
                term_n_week[1]*=1;
                if (term_n_week[1] <= week_number) {
                    topic_set = element.strand_name;
                    break;
                }
            }
        }
    
        cObj("strands_topics_assoc").innerText = topic_set;

        // get the long term plan

        // get the objectives of the plan
        var short_term_data = valObj("short_term_data");
        if (hasJsonStructure(short_term_data)) {
            // short term data
            short_term_data = JSON.parse(short_term_data);

            // display objectives
            var found_objective = 0;
            for (let index = 0; index < short_term_data.length; index++) {
                const element = short_term_data[index];
                if (element.date == original_date) {
                    displayObjectives(element.objectives, original_date);
                    found_objective = 1;
                }
            }
            if (found_objective == 0) {
                displayObjectives([], original_date);
            }

            // display activities
            var found_activities = 0;
            for (let index = 0; index < short_term_data.length; index++) {
                const element = short_term_data[index];
                if (element.date == original_date) {
                    displayActivities(element.activities, original_date);
                    found_activities = 1;
                }
            }
            if (found_activities == 0) {
                displayActivities([], original_date);
            }

            // display resources
            // start with notes files 

            // display notes
            var found_notes = 0;
            for (let index = 0; index < short_term_data.length; index++) {
                const element = short_term_data[index];
                if (element.date == original_date) {
                    displayNotes(element.resources.notes, original_date);
                    found_notes = 1;
                }
            }
            if (found_notes == 0) {
                displayNotes([], original_date);
            }

            // display book_refference
            var found_book_refference = 0;
            for (let index = 0; index < short_term_data.length; index++) {
                const element = short_term_data[index];
                if (element.date == original_date) {
                    displayBook_refference(element.resources.book_refference, original_date);
                    found_book_refference = 1;
                }
            }
            if (found_book_refference == 0) {
                displayBook_refference([], original_date);
            }

            // display youtube videos
            var found_youtube_reffs = 0;
            for (let index = 0; index < short_term_data.length; index++) {
                const element = short_term_data[index];
                if (element.date == original_date) {
                    displayYoutube(element.resources.videos, original_date);
                    found_youtube_reffs = 1;
                }
            }
            if (found_youtube_reffs == 0) {
                displayYoutube([], original_date);
            }

            // display youtube videos
            var found_quizes = 0;
            for (let index = 0; index < short_term_data.length; index++) {
                const element = short_term_data[index];
                if (element.date == original_date) {
                    displayQuiz(element.resources.quiz, original_date);
                    found_quizes = 1;
                }
            }
            if (found_quizes == 0) {
                displayQuiz([], original_date);
            }
            
            // display comments
            var found_comments = 0;
            for (let index = 0; index < short_term_data.length; index++) {
                const element = short_term_data[index];
                if (element.date == original_date) {
                    cObj("comments_appear_here").innerHTML = element.comments.length > 0 ? element.comments : "<h3 class='text-center text-secondary mt-1'><i class='bi bi-exclamation-triangle'></i></h3><p class='text-secondary text-center'>Comments are not set..</p>";
                    found_comments = 1;
                }
            }

            if (found_comments == 0) {
                cObj("comments_appear_here").innerHTML = "<h3 class='text-center text-secondary mt-1'><i class='bi bi-exclamation-triangle'></i></h3><p class='text-secondary text-center'>Comments are not set..</p>";
            }

            // show if completed
            cObj("complete_status").checked = false;
            for (let index = 0; index < short_term_data.length; index++) {
                const element = short_term_data[index];
                if (element.date == original_date) {
                    cObj("complete_status").checked = element.completed;
                }
            }
        }
    }
    setPopulators();
}

function displayObjectives(objectives, original_date) {
    var data_to_display = "";
    if (objectives.length > 0) {
        for (let ind = 0; ind < objectives.length; ind++) {
            const elem = objectives[ind];
            data_to_display += "<li class='list-group-item'>" + (ind + 1) + ". " + elem.value + " <span style='cursor:pointer;' class='text-danger trash_objective' id = 'trash_objective_" + elem.id + "'><i class='bi bi-trash'></i></span></li>";
        }
    } else {
        data_to_display += "<h3 class='text-center text-secondary mt-1'><i class='bi bi-exclamation-triangle'></i></h3><p class='text-secondary text-center'>No Objectives set for " + formatDate(original_date) + "!</p>"
    }
    cObj("display_objectives").innerHTML = data_to_display;

    // date deleted
    var trash_objective = document.getElementsByClassName("trash_objective");
    for (let index = 0; index < trash_objective.length; index++) {
        const element = trash_objective[index];
        element.addEventListener("click", function () {
            var this_id = this.id.substr(16);

            // todays date
            var todays_date = valObj("main_date_selector");
            // short term data
            var short_term_data = valObj("short_term_data");
            if (hasJsonStructure(short_term_data)) {
                // short term data
                short_term_data = JSON.parse(short_term_data);
                for (let ind = 0; ind < short_term_data.length; ind++) {
                    const elem = short_term_data[ind];
                    if (elem.date == todays_date) {
                        var new_objectives = [];
                        for (let inds = 0; inds < elem.objectives.length; inds++) {
                            const elems = elem.objectives[inds];
                            if (elems.id == this_id) {
                                continue;
                            }
                            new_objectives.push(elems);
                        }

                        // change the objective list
                        elem.objectives = new_objectives;
                        break;
                    }
                }

                cObj("short_term_data").value = JSON.stringify(short_term_data);
                displayData();
            }
        });
    }
}

function displayActivities(activities, original_date) {
    var data_to_display = "";
    if (activities.length > 0) {
        for (let ind = 0; ind < activities.length; ind++) {
            const elem = activities[ind];
            data_to_display += "<li class='list-group-item'>" + (ind + 1) + ". " + elem.value + " <span style='cursor:pointer;' class='text-danger trash_activities' id = 'trash_activities_" + elem.id + "'><i class='bi bi-trash'></i></span></li>";
        }
    } else {
        data_to_display += "<h3 class='text-center text-secondary mt-1'><i class='bi bi-exclamation-triangle'></i></h3><p class='text-secondary text-center'>No activities set for " + formatDate(original_date) + "!</p>"
    }
    cObj("activities_list_display").innerHTML = data_to_display;

    // delete activities
    // date deleted
    var trash_activities = document.getElementsByClassName("trash_activities");
    for (let index = 0; index < trash_activities.length; index++) {
        const element = trash_activities[index];
        element.addEventListener("click", function () {
            var this_id = this.id.substr(17);

            // todays date
            var todays_date = valObj("main_date_selector");
            // short term data
            var short_term_data = valObj("short_term_data");
            if (hasJsonStructure(short_term_data)) {
                // short term data
                short_term_data = JSON.parse(short_term_data);
                for (let ind = 0; ind < short_term_data.length; ind++) {
                    const elem = short_term_data[ind];
                    if (elem.date == todays_date) {
                        var new_activities = [];
                        for (let inds = 0; inds < elem.activities.length; inds++) {
                            const elems = elem.activities[inds];
                            if (elems.id == this_id) {
                                continue;
                            }
                            new_activities.push(elems);
                        }

                        // change the objective list
                        elem.activities = new_activities;
                        break;
                    }
                }

                cObj("short_term_data").value = JSON.stringify(short_term_data);
                displayData();
            }
        });
    }
}

function displayNotes(notes, original_date) {
    var data_to_display = "";
    if (notes.length > 0) {
        for (let ind = 0; ind < notes.length; ind++) {
            const elem = notes[ind];
            data_to_display += "<li class='list-group-item'>" + (ind + 1) + ". <a href='" + elem.public_path + "' target='_blank' class='link'><b>" + elem.title + "</b></a> <span style='cursor:pointer;' class='text-danger trash_notes' id = 'trash_notes_" + elem.id + "'><i class='bi bi-trash'></i></span></li>";
        }
    } else {
        data_to_display += "<h3 class='text-center text-secondary mt-1'><i class='bi bi-exclamation-triangle'></i></h3><p class='text-secondary text-center'>No notes set for " + formatDate(original_date) + "!</p>"
    }
    cObj("notes_list_display").innerHTML = data_to_display;

    var trash_notes = document.getElementsByClassName("trash_notes");
    for (let index = 0; index < trash_notes.length; index++) {
        const element = trash_notes[index];
        element.addEventListener("click", deleteFile);
    }
}

function deleteFile() {
    var this_id = this.id.substr(12);
    var short_term_data = valObj("short_term_data");
    if (hasJsonStructure(short_term_data)) {
        short_term_data = JSON.parse(short_term_data);

        var public_path = "";
        // loop through the dates
        for (let index = 0; index < short_term_data.length; index++) {
            const element = short_term_data[index];
            if (element.date == valObj("main_date_selector")) {
                var new_notes = [];
                // get the location of the file that ypu want to delete
                for (let ind = 0; ind < element.resources.notes.length; ind++) {
                    const elem = element.resources.notes[ind];
                    if (elem.id == this_id) {
                        public_path = elem.public_path;
                        continue;
                    }
                    new_notes.push(elem);
                }
                short_term_data[index].resources.notes = new_notes;
            }
        }

        cObj("short_term_data").value = JSON.stringify(short_term_data);
        // go ahead and delete the file from its location
        if (public_path.length > 0) {
            const xhr = new XMLHttpRequest();
            const form = document.getElementById('form_handlers_inside');
            const progressBar = document.getElementById('progress_bars');
            const progressBarText = document.getElementById('progress_bars');

            // postings
            xhr.open('POST', '/DeleteFiles');
            xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            xhr.upload.addEventListener('progress', function (e) {
                const percent = e.loaded / e.total * 100;
                progressBar.style.width = percent + '%';
                progressBar.setAttribute('aria-valuenow', percent);
                progressBarText.innerText = Math.round(percent) + '%';
            });
            xhr.addEventListener('load', function (e) {
                progressBar.style.width = '100%';
                progressBar.setAttribute('aria-valuenow', 100);
                progressBarText.innerText = '100%';

                cObj("error_handler_file_upload").innerHTML = "<p class='text-danger'>File deleted successfully!</p>";
                setTimeout(() => {
                    cObj("error_handler_file_upload").innerHTML = "";
                }, 3000);

            });
            xhr.addEventListener('error', function (e) {
                console.log('An error occurred while uploading the file.');
            });

            const formData = new FormData();
            formData.append('file_path', public_path);
            xhr.send(formData);
        }
        displayData();
    }
}



function displayBook_refference(book_refference, original_date) {
    var data_to_display = "";
    if (book_refference.length > 0) {
        for (let ind = 0; ind < book_refference.length; ind++) {
            const elem = book_refference[ind];
            data_to_display += "<li class='list-group-item'>" + (ind + 1) + ". " + elem.value + " <span style='cursor:pointer;' class='text-danger trash_book_refferences' id = 'trash_book_refferences_" + elem.id + "'><i class='bi bi-trash'></i></span></li>";
        }
    } else {
        data_to_display += "<h3 class='text-center text-secondary mt-1'><i class='bi bi-exclamation-triangle'></i></h3><p class='text-secondary text-center'>No book refference set for " + formatDate(original_date) + "!</p>"
    }
    cObj("book_refference_list_display").innerHTML = data_to_display;

    // book refferences
    var trash_book_refferences = document.getElementsByClassName("trash_book_refferences");
    for (let index = 0; index < trash_book_refferences.length; index++) {
        const element = trash_book_refferences[index];
        element.addEventListener("click", function () {
            var this_id = this.id.substr(23);

            // todays date
            var todays_date = valObj("main_date_selector");
            // short term data
            var short_term_data = valObj("short_term_data");
            if (hasJsonStructure(short_term_data)) {
                // short term data
                short_term_data = JSON.parse(short_term_data);
                for (let ind = 0; ind < short_term_data.length; ind++) {
                    const elem = short_term_data[ind];
                    if (elem.date == todays_date) {
                        var new_activities = [];
                        for (let inds = 0; inds < elem.resources.book_refference.length; inds++) {
                            const elems = elem.resources.book_refference[inds];
                            if (elems.id == this_id) {
                                continue;
                            }
                            new_activities.push(elems);
                        }

                        // change the objective list
                        elem.resources.book_refference = new_activities;
                        break;
                    }
                }

                cObj("short_term_data").value = JSON.stringify(short_term_data);
                displayData();
            }
        });
    }
}

function displayYoutube(youtube_vid_id, original_date) {
    var data_to_display = "";
    if (youtube_vid_id.length > 0) {
        for (let ind = 0; ind < youtube_vid_id.length; ind++) {
            const elem = youtube_vid_id[ind];
            // data_to_display+="<li class='list-group-item'>"+(ind+1)+". "+elem.value+" <span style='cursor:pointer;' class='text-danger trash_youtube_videos' id = 'trash_youtube_videos_"+elem.id+"'><i class='bi bi-trash'></i></span></li>";
            data_to_display += "<li class='list-group-item'>" + (ind + 1) + ". " + elem.title + " <span style='cursor:pointer;' class='text-danger trash_youtube_videos' id = 'trash_youtube_videos_" + elem.video_id + "'><i class='bi bi-trash'></i></span><div class='container'><iframe class='w-100' allow='' src='https://www.youtube.com/embed/" + elem.video_id + "' frameborder='0' allowfullscreen></iframe></div></li>";
        }
    } else {
        data_to_display += "<h3 class='text-center text-secondary mt-1'><i class='bi bi-exclamation-triangle'></i></h3><p class='text-secondary text-center'>No youtube videos uploaded for " + formatDate(original_date) + "!</p>"
    }
    cObj("youtube_videos_lists").innerHTML = data_to_display;

    // set the delete function exception
    var trash_youtube_videos = document.getElementsByClassName("trash_youtube_videos");
    for (let index = 0; index < trash_youtube_videos.length; index++) {
        const element = trash_youtube_videos[index];
        element.addEventListener("click", deleteYTVideo);
    }
}

function deleteYTVideo() {
    var this_id = this.id.substr(21);
    deleteVideo(this_id);

    // check the video delete permissions and try again next time
    // const xhr = new XMLHttpRequest();
    // const progressBar = document.getElementById('deleting_loaders');

    // // postings
    // xhr.open('GET', '/DeleteYT/'+this_id);
    // xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content')); 
    // xhr.upload.addEventListener('progress', function(e) {
    //     const percent = e.loaded / e.total * 100;
    //     progressBar.innerHTML = "<p class='text-success'>Deleting Please wait "+Math.round(percent)+"%.</p>";
    // });
    // xhr.addEventListener('load', function(e) {
    //     if (this.response == "Video deleted successfully: "+this_id) {
    //         progressBar.innerHTML = "<p class='text-success'>Deleted Successfull!</p>";
    //         setTimeout(() => {
    //             progressBar.innerHTML = "";
    //         }, 2000);

    //         // delete the data of the video from the short term data
    //         deleteVideo(this_id);
    //     }else{
    //         progressBar.innerHTML = "<p class='text-danger'>Click the authorize button then try again!</p>";
    //     }

    // });
    // xhr.addEventListener('error', function(e) {
    //     console.log('An error occurred while uploading the file.');
    // });

    // const formData = new FormData();
    // formData.append('video_id', this_id);
    // xhr.send(formData);
}

function deleteVideo(youtube_vid_id) {
    var todays_date = valObj("main_date_selector");
    var short_term_data = valObj("short_term_data");
    if (hasJsonStructure(short_term_data)) {
        // short term data
        short_term_data = JSON.parse(short_term_data);
        for (let ind = 0; ind < short_term_data.length; ind++) {
            const elem = short_term_data[ind];
            if (elem.date == todays_date) {
                var new_videos = [];
                for (let inds = 0; inds < elem.resources.videos.length; inds++) {
                    const elems = elem.resources.videos[inds];
                    if (elems.video_id == youtube_vid_id) {
                        continue;
                    }
                    new_videos.push(elems);
                }

                // change the objective list
                elem.resources.videos = new_videos;
                break;
            }
        }

        cObj("short_term_data").value = JSON.stringify(short_term_data);
        displayData();
    }
}

function displayQuiz(data_quiz, original_date) {
    var data_to_display = "";
    if (data_quiz.length > 0) {
        for (let ind = 0; ind < data_quiz.length; ind++) {
            const elem = data_quiz[ind];
            data_to_display += "<li class='list-group-item'>" + (ind + 1) + ". " + elem.value + " <span style='cursor:pointer;' class='text-danger trash_quizes' id = 'trash_quizes_" + elem.id + "'><i class='bi bi-trash'></i></span></li>";
        }
    } else {
        data_to_display += "<h3 class='text-center text-secondary mt-1'><i class='bi bi-exclamation-triangle'></i></h3><p class='text-secondary text-center'>No quiz set for " + formatDate(original_date) + "!</p>"
    }
    cObj("quiz_list").innerHTML = data_to_display;

    // book refferences
    var trash_quizes = document.getElementsByClassName("trash_quizes");
    for (let index = 0; index < trash_quizes.length; index++) {
        const element = trash_quizes[index];
        element.addEventListener("click", function () {
            var this_id = this.id.substr(13);

            // todays date
            var todays_date = valObj("main_date_selector");
            // short term data
            var short_term_data = valObj("short_term_data");
            if (hasJsonStructure(short_term_data)) {
                // short term data
                short_term_data = JSON.parse(short_term_data);
                for (let ind = 0; ind < short_term_data.length; ind++) {
                    const elem = short_term_data[ind];
                    if (elem.date == todays_date) {
                        var new_quiz = [];
                        for (let inds = 0; inds < elem.resources.quiz.length; inds++) {
                            const elems = elem.resources.quiz[inds];
                            if (elems.id == this_id) {
                                continue;
                            }
                            new_quiz.push(elems);
                        }

                        // change the objective list
                        elem.resources.quiz = new_quiz;
                        break;
                    }
                }

                cObj("short_term_data").value = JSON.stringify(short_term_data);
                displayData();
            }
        });
    }
}

// save an objective
cObj("save_objectives").onclick = function () {
    var err = checkBlank("plan_objectives");
    if (err == 0) {
        var short_term_data = valObj("short_term_data");
        var term_n_weeks = getTermWeek(dates_details, valObj("main_date_selector"));

        // check if has json structure
        if (term_n_weeks[0] == "Not Set") {
            cObj("text_error_display").innerText = "You cannot create a plan on a date thats not included in the academic calender, Its considered a holiday or a weekend!";
            cObj("error_names_are_in").click();
        } else {
            if (hasJsonStructure(short_term_data)) {
                short_term_data = JSON.parse(short_term_data);
                var date_selected = valObj("main_date_selector");

                // loop through the data
                var not_present = 0;
                for (let index = 0; index < short_term_data.length; index++) {
                    const element = short_term_data[index];
                    if (date_selected == element.date) {
                        var objectives = element.objectives;
                        var latest_id = 0;
                        if (objectives.length > 0) {
                            for (let inds = 0; inds < objectives.length; inds++) {
                                const element = objectives[inds];
                                if (element.id * 1 >= latest_id) {
                                    latest_id = (element.id * 1);
                                }
                            }
                        }

                        // latest add
                        latest_id += 1;
                        var new_objective = {
                            id: latest_id,
                            value: valObj("plan_objectives")
                        }
                        short_term_data[index].objectives.push(new_objective);
                        not_present = 1;
                        break;
                    }
                }

                // if not present add new
                if (not_present == 0) {
                    var new_plan = {
                        objectives: [{
                            id: 1,
                            value: valObj("plan_objectives")
                        }],
                        activities: [],
                        resources: {
                            notes: [],
                            videos: [],
                            book_refference: [],
                            quiz: []
                        },
                        comments: "",
                        week: term_n_weeks[1],
                        term: term_n_weeks[0],
                        date: valObj("main_date_selector"),
                        completed: false
                    }

                    short_term_data.push(new_plan);
                }

                // display the data
                cObj("short_term_data").value = JSON.stringify(short_term_data);
                displayData();

                cObj("plan_objectives").value = "";
            }
        }
    }
}

// save an objective
cObj("save_activities").onclick = function () {
    var err = checkBlank("plan_activities");
    if (err == 0) {
        var short_term_data = valObj("short_term_data");
        var term_n_weeks = getTermWeek(dates_details, valObj("main_date_selector"));

        // check if has json structure
        if (term_n_weeks[0] == "Not Set") {
            cObj("text_error_display").innerText = "You cannot create a plan on a date thats not included in the academic calender, Its considered a holiday or a weekend!";
            cObj("error_names_are_in").click();
        } else {
            if (hasJsonStructure(short_term_data)) {
                short_term_data = JSON.parse(short_term_data);
                var date_selected = valObj("main_date_selector");

                // loop through the data
                var not_present = 0;
                for (let index = 0; index < short_term_data.length; index++) {
                    const element = short_term_data[index];
                    if (date_selected == element.date) {
                        var activities = element.activities;
                        var latest_id = 0;
                        if (activities.length > 0) {
                            for (let inds = 0; inds < activities.length; inds++) {
                                const element = activities[inds];
                                if (element.id * 1 >= latest_id) {
                                    latest_id = (element.id * 1);
                                }
                            }
                        }

                        // latest add
                        latest_id += 1;
                        var new_objective = {
                            id: latest_id,
                            value: valObj("plan_activities")
                        }
                        short_term_data[index].activities.push(new_objective);
                        not_present = 1;
                        break;
                    }
                }

                // if not present add new
                if (not_present == 0) {
                    var new_plan = {
                        objectives: [],
                        activities: [{
                            id: 1,
                            value: valObj("plan_activities")
                        }],
                        resources: {
                            notes: [],
                            videos: [],
                            book_refference: [],
                            quiz: []
                        },
                        comments: "",
                        week: term_n_weeks[1],
                        term: term_n_weeks[0],
                        date: valObj("main_date_selector"),
                        completed: false
                    }

                    short_term_data.push(new_plan);
                }

                // display the data
                cObj("short_term_data").value = JSON.stringify(short_term_data);
                displayData();
                cObj("plan_activities").value = "";
            }
        }
    }
}


cObj("complete_status").onchange = function () {
    var short_term_data = valObj("short_term_data");
    var term_n_weeks = getTermWeek(dates_details, valObj("main_date_selector"));

    // check if has json structure
    if (term_n_weeks[0] == "Not Set") {
        cObj("text_error_display").innerText = "You cannot create a plan on a date thats not included in the academic calender, Its considered a holiday or a weekend!";
        cObj("error_names_are_in").click();
    } else {
        if (hasJsonStructure(short_term_data)) {
            short_term_data = JSON.parse(short_term_data);
            var date_selected = valObj("main_date_selector");

            // loop through the data
            var not_present = 0;
            for (let index = 0; index < short_term_data.length; index++) {
                const element = short_term_data[index];
                if (date_selected == element.date) {
                    element.completed = cObj("complete_status").checked;
                    not_present = 1;
                    break;
                }
            }

            // if not present add new
            if (not_present == 0) {
                var new_plan = {
                    objectives: [],
                    activities: [],
                    resources: {
                        notes: [],
                        videos: [],
                        book_refference: [],
                        quiz: []
                    },
                    comments: "",
                    week: term_n_weeks[1],
                    term: term_n_weeks[0],
                    date: valObj("main_date_selector"),
                    completed: cObj("complete_status").checked
                }

                short_term_data.push(new_plan);
            }

            // display the data
            cObj("short_term_data").value = JSON.stringify(short_term_data);
            displayData();
        }
    }
}

// add add_book_refferences
cObj("add_book_refferences").onclick = function () {
    var err = checkBlank("book_refferences");
    if (err == 0) {
        var short_term_data = valObj("short_term_data");
        var term_n_weeks = getTermWeek(dates_details, valObj("main_date_selector"));

        // check if has json structure
        if (term_n_weeks[0] == "Not Set") {
            cObj("text_error_display").innerText = "You cannot create a plan on a date thats not included in the academic calender, Its considered a holiday or a weekend!";
            cObj("error_names_are_in").click();
        } else {
            if (hasJsonStructure(short_term_data)) {
                short_term_data = JSON.parse(short_term_data);
                var date_selected = valObj("main_date_selector");

                // loop through the data
                var not_present = 0;
                for (let index = 0; index < short_term_data.length; index++) {
                    const element = short_term_data[index];
                    if (date_selected == element.date) {
                        var book_refference = element.resources.book_refference;
                        var latest_id = 0;
                        if (book_refference.length > 0) {
                            for (let inds = 0; inds < book_refference.length; inds++) {
                                const element = book_refference[inds];
                                if (element.id * 1 >= latest_id) {
                                    latest_id = (element.id * 1);
                                }
                            }
                        }

                        // latest add
                        latest_id += 1;
                        var book_refference = {
                            id: latest_id,
                            value: valObj("book_refferences")
                        }
                        short_term_data[index].resources.book_refference.push(book_refference);
                        not_present = 1;
                        break;
                    }
                }

                // if not present add new
                if (not_present == 0) {
                    var new_plan = {
                        objectives: [],
                        activities: [],
                        resources: {
                            notes: [],
                            videos: [],
                            book_refference: [{
                                id: 1,
                                value: valObj("book_refferences")
                            }],
                            quiz: []
                        },
                        comments: "",
                        week: term_n_weeks[1],
                        term: term_n_weeks[0],
                        date: valObj("main_date_selector"),
                        completed: false
                    }

                    short_term_data.push(new_plan);
                }

                // display the data
                cObj("short_term_data").value = JSON.stringify(short_term_data);
                displayData();
                cObj("book_refferences").value = "";
            }
        }
    }
}

// set youtube video ids
// add add_book_refferences
cObj("add_youtube_video_ids").onclick = function () {
    var err = checkBlank("youtube_video_ids");
    err += checkBlank("video_tittles");
    err += checkBlank("video_descriptions");
    err += checkBlank("video_privacy_status");
    if (err == 0) {
        var short_term_data = valObj("short_term_data");
        var term_n_weeks = getTermWeek(dates_details, valObj("main_date_selector"));

        // check if has json structure
        if (term_n_weeks[0] == "Not Set") {
            cObj("text_error_display").innerText = "You cannot create a plan on a date thats not included in the academic calender, Its considered a holiday or a weekend!";
            cObj("error_names_are_in").click();
        } else {
            var file_data = {
                title: valObj("video_tittles"),
                description: valObj("video_descriptions"),
                privacy: valObj("video_privacy_status"),
                video_id: valObj("youtube_video_ids")
            }

            // go through the files that are there and get the latest id
            var short_term_data = valObj("short_term_data");
            var latest_id = 0;
            if (hasJsonStructure(short_term_data)) {
                short_term_data = JSON.parse(short_term_data);

                // loop through the dates
                var not_present = 0;
                for (let index = 0; index < short_term_data.length; index++) {
                    const element = short_term_data[index];
                    if (element.date == valObj("main_date_selector")) {
                        var videos = element.resources.videos;
                        for (let ind = 0; ind < videos.length; ind++) {
                            const elems = videos[ind];
                            if (elems.id >= latest_id) {
                                latest_id = elems.id;
                            }
                        }

                        // latest id
                        latest_id += 1;
                        file_data.id = latest_id;

                        short_term_data[index].resources.videos.push(file_data);
                        not_present = 1;
                    }
                }
                // if not present add new
                if (not_present == 0) {
                    file_data.id = 1;
                    var new_plan = {
                        objectives: [],
                        activities: [],
                        resources: {
                            notes: [],
                            videos: [file_data],
                            book_refference: [],
                            quiz: []
                        },
                        comments: "",
                        week: term_n_weeks[1],
                        term: term_n_weeks[0],
                        date: valObj("main_date_selector"),
                        completed: false
                    }

                    short_term_data.push(new_plan);
                }
                // turn it back to string
                cObj("short_term_data").value = JSON.stringify(short_term_data);

                // reset the fields
                cObj("youtube_video_ids").value = "";
                cObj("video_tittles").value = "";
                cObj("video_descriptions").value = "";
                displayData();
            }
        }
    }
}

// add set_a_quizes
cObj("set_a_quizes").onclick = function () {
    var err = checkBlank("plan_quizes");
    if (err == 0) {
        var short_term_data = valObj("short_term_data");
        var term_n_weeks = getTermWeek(dates_details, valObj("main_date_selector"));

        // check if has json structure
        if (term_n_weeks[0] == "Not Set") {
            cObj("text_error_display").innerText = "You cannot create a plan on a date thats not included in the academic calender, Its considered a holiday or a weekend!";
            cObj("error_names_are_in").click();
        } else {
            if (hasJsonStructure(short_term_data)) {
                short_term_data = JSON.parse(short_term_data);
                var date_selected = valObj("main_date_selector");

                // loop through the data
                var not_present = 0;
                for (let index = 0; index < short_term_data.length; index++) {
                    const element = short_term_data[index];
                    if (date_selected == element.date) {
                        var quiz = element.resources.quiz;
                        var latest_id = 0;
                        if (quiz.length > 0) {
                            for (let inds = 0; inds < quiz.length; inds++) {
                                const element = quiz[inds];
                                if (element.id * 1 >= latest_id) {
                                    latest_id = (element.id * 1);
                                }
                            }
                        }

                        // latest add
                        latest_id += 1;
                        var quiz = {
                            id: latest_id,
                            value: valObj("plan_quizes")
                        }
                        short_term_data[index].resources.quiz.push(quiz);
                        not_present = 1;
                        break;
                    }
                }

                // if not present add new
                if (not_present == 0) {
                    var new_plan = {
                        objectives: [],
                        activities: [],
                        resources: {
                            notes: [],
                            videos: [],
                            book_refference: [],
                            quiz: [{
                                id: 1,
                                value: valObj("plan_quizes")
                            }]
                        },
                        comments: "",
                        week: term_n_weeks[1],
                        term: term_n_weeks[0],
                        date: valObj("main_date_selector"),
                        completed: false
                    }

                    short_term_data.push(new_plan);
                }

                // display the data
                cObj("short_term_data").value = JSON.stringify(short_term_data);
                displayData();
                cObj("plan_quizes").value = "";
            }
        }
    }
}

cObj("set_a_comments").onclick = function () {
    var err = checkBlank("plan_comments");

    if (err == 0) {
        var short_term_data = valObj("short_term_data");
        var term_n_weeks = getTermWeek(dates_details, valObj("main_date_selector"));

        // check if has json structure
        if (term_n_weeks[0] == "Not Set") {
            cObj("text_error_display").innerText = "You cannot create a plan on a date thats not included in the academic calender, Its considered a holiday or a weekend!";
            cObj("error_names_are_in").click();
        } else {
            if (hasJsonStructure(short_term_data)) {
                short_term_data = JSON.parse(short_term_data);
                var date_selected = valObj("main_date_selector");

                // loop through the data
                var not_present = 0;
                for (let index = 0; index < short_term_data.length; index++) {
                    const element = short_term_data[index];
                    if (date_selected == element.date) {
                        short_term_data[index].comments = valObj("plan_comments");
                        not_present = 1;
                        break;
                    }
                }

                // if not present add new
                if (not_present == 0) {
                    var new_plan = {
                        objectives: [],
                        activities: [],
                        resources: {
                            notes: [],
                            videos: [],
                            book_refference: [],
                            quiz: []
                        },
                        comments: valObj("plan_comments"),
                        week: term_n_weeks[1],
                        term: term_n_weeks[0],
                        date: valObj("main_date_selector"),
                        completed: false
                    }

                    short_term_data.push(new_plan);
                }

                // display the data
                cObj("short_term_data").value = JSON.stringify(short_term_data);
                displayData();
                cObj("plan_comments").value = "";
            }
        }
    }
}
const form_youtube = document.getElementById('upload_youtube_video');
form_youtube.addEventListener('submit', function (e) {
    e.preventDefault();
    uploadYoutube();
});

function uploadYoutube() {
    const xhr = new XMLHttpRequest();
    const form = document.getElementById('upload_youtube_video');
    const fileInput = document.getElementById('youtube_videos_uploads');
    const progressBar = document.getElementById('progress_bars_youtubes');
    const progressBarText = document.getElementById('progress_bars_youtubes');

    var term_n_weeks = getTermWeek(dates_details, valObj("main_date_selector"));

    // check if has json structure
    if (term_n_weeks[0] != "Not Set") {
        const maxFileSize = 2000 * 1024 * 1024; // 2GB in bytes
        const file = fileInput.files[0];
        if (file && file.size <= maxFileSize) {
            // check if the file is less than 2mbs
            cObj("file_progress_bars_youtube").classList.remove("hide");

            xhr.open('POST', '/UploadYoutube');
            xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            xhr.upload.addEventListener('progress', function (e) {
                const percent = e.loaded / e.total * 100;
                progressBar.style.width = percent + '%';
                progressBar.setAttribute('aria-valuenow', percent);
                progressBarText.innerText = Math.round(percent) + '%';

                if (Math.round(percent) < 10) {
                    cObj("upload_youtube").disabled = true;
                    cObj("auth-btn").disabled = true;
                }

                if (Math.round(percent) == 100) {
                    cObj("youtube_upload_err").innerHTML = "<p class='text-success'>Video is processing. Please wait!</p>";
                }
            });
            xhr.addEventListener('load', function (e) {
                progressBar.style.width = '100%';
                progressBar.setAttribute('aria-valuenow', 100);
                progressBarText.innerText = '100%';

                // after successfully upload get the value returned
                // show the success message
                setTimeout(() => {
                    cObj("file_progress_bars_youtube").classList.add("hide");
                }, 1000);
                cObj("youtube_upload_err").innerHTML = "<p class='text-success'>File uploaded successfully!</p>";
                setTimeout(() => {
                    cObj("youtube_upload_err").innerHTML = "";
                }, 3000);

                // empty the whole form
                cObj("upload_youtube").disabled = false;
                cObj("auth-btn").disabled = false;

                // add the video details
                // first get the video files and condition it to be uploaded and displayed
                // get the returned value
                var response = this.response;
                if (hasJsonStructure(response)) {
                    response = JSON.parse(response);
                    // console.log(response[1]);
                    // create the file
                    if (response.original.message == "Video uploaded successfully") {
                        var file_data = {
                            title: valObj("video_name"),
                            description: valObj("video_description"),
                            privacy: valObj("video_privacy"),
                            video_id: response.original.id
                        }

                        // go through the files that are there and get the latest id
                        var short_term_data = valObj("short_term_data");
                        var latest_id = 0;
                        if (hasJsonStructure(short_term_data)) {
                            short_term_data = JSON.parse(short_term_data);

                            // loop through the dates
                            var not_present = 0;
                            for (let index = 0; index < short_term_data.length; index++) {
                                const element = short_term_data[index];
                                if (element.date == valObj("main_date_selector")) {
                                    var videos = element.resources.videos;
                                    for (let ind = 0; ind < videos.length; ind++) {
                                        const elems = videos[ind];
                                        if (elems.id >= latest_id) {
                                            latest_id = elems.id;
                                        }
                                    }

                                    // latest id
                                    latest_id += 1;
                                    file_data.id = latest_id;

                                    short_term_data[index].resources.videos.push(file_data);
                                    not_present = 1;
                                }
                            }
                            // if not present add new
                            if (not_present == 0) {
                                file_data.id = 1;
                                var new_plan = {
                                    objectives: [],
                                    activities: [],
                                    resources: {
                                        notes: [],
                                        videos: [file_data],
                                        book_refference: [],
                                        quiz: []
                                    },
                                    comments: "",
                                    week: term_n_weeks[1],
                                    term: term_n_weeks[0],
                                    date: valObj("main_date_selector"),
                                    completed: false
                                }

                                short_term_data.push(new_plan);
                            }
                            // turn it back to string
                            cObj("short_term_data").value = JSON.stringify(short_term_data);
                        }
                        displayData();
                    } else {
                        cObj("youtube_upload_err").innerHTML = "<p class='text-danger'>Kindly click the authorize button and try again!</p>";
                        cObj("upload_youtube").disabled = false;
                        cObj("auth-btn").disabled = false;
                    }
                }

                // upload video reset
                cObj("upload_youtube_video").reset();
            });
            xhr.addEventListener('error', function (e) {
                cObj("youtube_upload_err").innerHTML = "<p class='text-danger'>An error occured while uploading the video on youtube! Try again!</p>";

            });

            const formData = new FormData();
            formData.append('file', fileInput.files[0]);
            formData.append('video_name', valObj("video_name"));
            formData.append('video_description', valObj("video_description"));
            formData.append('video_privacy', valObj("video_privacy"));
            xhr.send(formData);
        } else {
            alert("Your file should be less than 2GB");
        }
    } else {
        alert("You cannot create a plan on a date thats not included in the academic calender, Its considered a holiday or a weekend!");
    }
}

const form = document.getElementById('form_handlers_inside');
form.addEventListener('submit', function (e) {
    e.preventDefault();
    uploadFile();
});

function uploadFile() {
    const xhr = new XMLHttpRequest();
    const form = document.getElementById('form_handlers_inside');
    const fileInput = document.getElementById('notes_file_accept');
    const progressBar = document.getElementById('progress_bars');
    const progressBarText = document.getElementById('progress_bars');

    var term_n_weeks = getTermWeek(dates_details, valObj("main_date_selector"));

    // check if has json structure
    if (term_n_weeks[0] != "Not Set") {
        const maxFileSize = 2 * 1024 * 1024; // 2MB in bytes
        const file = fileInput.files[0];
        if (file && file.size <= maxFileSize) {
            // check if the file is less than 2mbs
            cObj("file_progress_bars").classList.remove("hide");

            xhr.open('POST', '/UploadNotesFiles');
            xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            xhr.upload.addEventListener('progress', function (e) {
                const percent = e.loaded / e.total * 100;
                progressBar.style.width = percent + '%';
                progressBar.setAttribute('aria-valuenow', percent);
                progressBarText.innerText = Math.round(percent) + '%';

                if (Math.round(percent) == 100) {
                    cObj("youtube_upload_err").innerHTML = "<p class='text-success'>File is processing. Please wait!</p>";
                }
            });
            xhr.addEventListener('load', function (e) {
                progressBar.style.width = '100%';
                progressBar.setAttribute('aria-valuenow', 100);
                progressBarText.innerText = '100%';

                // after successfully upload get the value returned
                // show the success message
                setTimeout(() => {
                    cObj("file_progress_bars").classList.add("hide");
                }, 1000);
                cObj("error_handler_file_upload").innerHTML = "<p class='text-success'>File uploaded successfully!</p>";
                setTimeout(() => {
                    cObj("error_handler_file_upload").innerHTML = "";
                }, 3000);

                // get the returned value
                var response = this.response;
                if (hasJsonStructure(response)) {
                    response = JSON.parse(response);
                    // console.log(response[1]);
                    // create the file 
                    var file_data = {
                        title: response[0],
                        location: response[2] + "/" + response[1],
                        public_path: response[3] + "/" + response[1]
                    }

                    // go through the files that are there and get the latest id
                    var short_term_data = valObj("short_term_data");
                    var latest_id = 0;
                    if (hasJsonStructure(short_term_data)) {
                        short_term_data = JSON.parse(short_term_data);

                        // loop through the dates
                        var not_present = 0;
                        for (let index = 0; index < short_term_data.length; index++) {
                            const element = short_term_data[index];
                            if (element.date == valObj("main_date_selector")) {
                                var notes = element.resources.notes;
                                console.log(notes);
                                for (let ind = 0; ind < notes.length; ind++) {
                                    const elems = notes[ind];
                                    if (elems.id >= latest_id) {
                                        latest_id = elems.id;
                                    }
                                }

                                // latest id
                                latest_id += 1;
                                file_data.id = latest_id;

                                short_term_data[index].resources.notes.push(file_data);
                                not_present = 1;
                            }
                        }
                        // if not present add new
                        if (not_present == 0) {
                            file_data.id = 1;
                            var new_plan = {
                                objectives: [],
                                activities: [],
                                resources: {
                                    notes: [file_data],
                                    videos: [],
                                    book_refference: [],
                                    quiz: []
                                },
                                comments: "",
                                week: term_n_weeks[1],
                                term: term_n_weeks[0],
                                date: valObj("main_date_selector"),
                                completed: false
                            }

                            short_term_data.push(new_plan);
                        }
                        // turn it back to string
                        cObj("short_term_data").value = JSON.stringify(short_term_data);
                    }
                    displayData();
                }

                fileInput.value = "";
            });
            xhr.addEventListener('error', function (e) {
                console.log('An error occurred while uploading the file.');
            });

            const formData = new FormData();
            formData.append('file', fileInput.files[0]);
            formData.append('subject_id', valObj("subject_id_file"));
            formData.append('class_selected', valObj("class_id_file"));
            xhr.send(formData);
        } else {
            alert("Your file should be less than 2mbs");
        }
    } else {
        alert("You cannot create a plan on a date thats not included in the academic calender, Its considered a holiday or a weekend!");
    }
}

function setPopulators() {
    var objectives = [];
    var activities = [];
    var resources = [];

    // get the term and week
    var main_date_selector = cObj("main_date_selector").value;
    var term_n_week = getTermWeek(dates_details, main_date_selector);

    for (let index = 0; index < medium_term_plan.length; index++) {
        const element = medium_term_plan[index];
        if (element.week_name == term_n_week[1] && element.term_name == term_n_week[0]) {
            var obj = element.objectives;
            var act = element.activities;
            var res = element.resources;

            // objectives
            for (let ind = 0; ind < obj.length; ind++) {
                const elem = obj[ind];
                objectives.push(elem.value);
            }

            // activities
            for (let ind = 0; ind < act.length; ind++) {
                const elem = act[ind];
                activities.push(elem.value);
            }

            // resources
            for (let ind = 0; ind < res.length; ind++) {
                const elem = res[ind];
                resources.push(elem.value);
            }
        }
    }

    // set the populators for the following input boxes
    autocomplete(cObj("plan_objectives"),objectives);
    autocomplete(cObj("plan_activities"),activities);
    autocomplete(cObj("plan_resources"),resources);
}


function autocomplete(inp, arr) {
    /*the autocomplete function takes two arguments,
    the text field element and an array of possible autocompleted values:*/
    var currentFocus;
    /*execute a function when someone writes in the text field:*/
    inp.addEventListener("input", function(e) {
        var a, b, i, val = this.value;
        /*close any already open lists of autocompleted values*/
        closeAllLists();
        if (!val) {
            return false;
        }
        currentFocus = -1;
        /*create a DIV element that will contain the items (values):*/
        a = document.createElement("DIV");
        a.setAttribute("id", this.id + "autocomplete-list");
        a.setAttribute("class", "autocomplete-items");
        /*append the DIV element as a child of the autocomplete container:*/
        this.parentNode.appendChild(a);
        /*for each item in the array...*/
        var counter = 0;
        for (i = 0; i < arr.length; i++) {
            if (counter > 10) {
                break;
            }
            /*check if the item starts with the same letters as the text field value:*/
            if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                /*create a DIV element for each matching element:*/
                b = document.createElement("DIV");
                /*make the matching letters bold:*/
                b.innerHTML = "<p class='text-primary' ><strong class=''>" +arr[i].substr(0, val.length)+ "</strong>"+arr[i].substr(val.length);
                // b.innerHTML += arr[i].substr(val.length);
                /*insert a input field that will hold the current array item's value:*/
                b.innerHTML += "<input type='hidden' value='" + arr[i] + "'></p>";
                /*execute a function when someone clicks on the item value (DIV element):*/
                b.addEventListener("click", function(e) {
                    /*insert the value for the autocomplete text field:*/
                    inp.value = this.getElementsByTagName("input")[0].value;
                    /*close the list of autocompleted values,
                    (or any other open lists of autocompleted values:*/
                    closeAllLists();
                });
                a.appendChild(b);
                counter++;
            }
            // console.log(counter);
        }
    });
    /*execute a function presses a key on the keyboard:*/
    inp.addEventListener("keydown", function(e) {
        var x = document.getElementById(this.id + "autocomplete-list");
        if (x) x = x.getElementsByTagName("div");
        if (e.keyCode == 40) {
            /*If the arrow DOWN key is pressed,
            increase the currentFocus variable:*/
            currentFocus++;
            /*and and make the current item more visible:*/
            addActive(x);
        } else if (e.keyCode == 38) { //up
            /*If the arrow UP key is pressed,
            decrease the currentFocus variable:*/
            currentFocus--;
            /*and and make the current item more visible:*/
            addActive(x);
        } else if (e.keyCode == 13) {
            /*If the ENTER key is pressed, prevent the form from being submitted,*/
            e.preventDefault();
            if (currentFocus > -1) {
                /*and simulate a click on the "active" item:*/
                if (x) x[currentFocus].click();
            }
        }
    });

    function addActive(x) {
        /*a function to classify an item as "active":*/
        if (!x) return false;
        /*start by removing the "active" class on all items:*/
        removeActive(x);
        if (currentFocus >= x.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = (x.length - 1);
        /*add class "autocomplete-active":*/
        x[currentFocus].classList.add("autocomplete-active");
    }

    function removeActive(x) {
        /*a function to remove the "active" class from all autocomplete items:*/
        for (var i = 0; i < x.length; i++) {
            x[i].classList.remove("autocomplete-active");
        }
    }

    function closeAllLists(elmnt) {
        /*close all autocomplete lists in the document,
        except the one passed as an argument:*/
        var x = document.getElementsByClassName("autocomplete-items");
        for (var i = 0; i < x.length; i++) {
            if (elmnt != x[i] && elmnt != inp) {
                x[i].parentNode.removeChild(x[i]);
            }
        }
    }
    /*execute a function when someone clicks in the document:*/
    document.addEventListener("click", function(e) {
        closeAllLists(e.target);
    });
}