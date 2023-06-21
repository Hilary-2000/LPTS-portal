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

window.onload = function () {
    displayData();
}

cObj("display_data").onclick = function () {
    displayData();
}

function displayData() {
    var date = valObj("selected_date");
    var err = checkBlank("selected_date");
    if (err == 0) {
        cObj("date_lm_inside").innerText = formatDate(date);
        // get the data for that particular date
        var short_term_data = {};
        for (let index = 0; index < short_term_plan.length; index++) {
            const element = short_term_plan[index];
            if (element.date == date) {
                short_term_data = element;
                break;
            }
        }
    
        // display the data according to the date
        if (Object.keys(short_term_data).length > 0) {
            // get the data from the json element
            var notes = short_term_data.resources.notes;
            var book_refference = short_term_data.resources.book_refference;
            var quiz = short_term_data.resources.quiz;
            var videos = short_term_data.resources.videos;
    
            // show document details
            if (notes.length>0) {
                var data_to_display = "<table class='table table-borderless datatable' id='my_tables'><thead><tr><th scope='col'>#</th><th scope='col'>Document Name</th><th scope='col'>Action</th></tr></thead><tbody>";
                for (let index = 0; index < notes.length; index++) {
                    const element = notes[index];
                    data_to_display+="<tr><td scope='row'>"+(index+1)+"</td>";
                    data_to_display+="<td>"+element.title+"</td>";
                    data_to_display+="<td><a href='"+element.public_path+"' download='"+element.title+"' class='link'><b><i class='bi bi-download'></i> download</b></a></td><tr>";
                }
                data_to_display+="</tbody></table>";
                cObj("table_data").innerHTML = data_to_display;
            }else{
                cObj("table_data").innerHTML = "<h3 class='text-center text-secondary mt-1'><i class='bi bi-exclamation-triangle'></i></h3><p class='text-secondary text-center'>No notes & documents set for " + formatDate(date) + "!</p>";
            }
    
            // show the book refferences
            if (book_refference.length > 0) {
                var data_to_display = "";
                for (let index = 0; index < book_refference.length; index++) {
                    const element = book_refference[index];
                    data_to_display+="<li class='list-group-item'>"+(index+1)+". "+element.value+"</li>";
                }
                cObj("book_refference_list").innerHTML = data_to_display;
            }else{
                cObj("book_refference_list").innerHTML = "<h3 class='text-center text-secondary mt-1'><i class='bi bi-exclamation-triangle'></i></h3><p class='text-secondary text-center'>No Book Refference set for " + formatDate(date) + "!</p>";
            }
    
            // show the book refferences
            if (videos.length > 0) {
                var data_to_display = "";
                for (let index = 0; index < videos.length; index++) {
                    const element = videos[index];
                    data_to_display+="<li class='list-group-item'>"+(index+1)+". "+element.title+" <div class='container'><iframe class='w-100' allow='' src='https://www.youtube.com/embed/" + element.video_id + "' frameborder='0' allowfullscreen></iframe></div></li>";
                }
                cObj("learning_videos_refferences").innerHTML = data_to_display;
            }else{
                cObj("learning_videos_refferences").innerHTML = "<h3 class='text-center text-secondary mt-1'><i class='bi bi-exclamation-triangle'></i></h3><p class='text-secondary text-center'>No Learning videos set for " + formatDate(date) + "!</p>";
            }
    
            // the quiz should be displayed
    
            // show the book refferences
            if (quiz.length > 0) {
                var data_to_display = "";
                for (let index = 0; index < quiz.length; index++) {
                    const element = quiz[index];
                    data_to_display+="<li class='list-group-item'>"+(index+1)+". "+element.value+"</li>";
                }
                cObj("quiz_lists_refferences").innerHTML = data_to_display;
            }else{
                cObj("quiz_lists_refferences").innerHTML = "<h3 class='text-center text-secondary mt-1'><i class='bi bi-exclamation-triangle'></i></h3><p class='text-secondary text-center'>No quiz set for " + formatDate(date) + "!</p>";
            }
        }else{
            cObj("table_data").innerHTML = "<h3 class='text-center text-secondary mt-1'><i class='bi bi-exclamation-triangle'></i></h3><p class='text-secondary text-center'>No notes & documents set for " + formatDate(date) + "!</p>";
            cObj("book_refference_list").innerHTML = "<h3 class='text-center text-secondary mt-1'><i class='bi bi-exclamation-triangle'></i></h3><p class='text-secondary text-center'>No Book Refference set for " + formatDate(date) + "!</p>";
            cObj("learning_videos_refferences").innerHTML = "<h3 class='text-center text-secondary mt-1'><i class='bi bi-exclamation-triangle'></i></h3><p class='text-secondary text-center'>No Learning videos set for " + formatDate(date) + "!</p>";
            cObj("quiz_lists_refferences").innerHTML = "<h3 class='text-center text-secondary mt-1'><i class='bi bi-exclamation-triangle'></i></h3><p class='text-secondary text-center'>No quiz set for " + formatDate(date) + "!</p>";
        }
    }
    if (cObj("my_tables") != undefined) {
        // const table = document.querySelector('#my_tables');

        // // check if the table has a DataTable instance already
        // const hasDataTable = table.classList.contains('dataTable');

        // // if the table has a DataTable instance, destroy it before re-initializing
        // if (hasDataTable) {
        //     const dataTable = simpleDatatables.dataTable(table);
        //     dataTable.destroy();
        // }
        
        // new simpleDatatables.DataTable(table);
    }

    var sub_strand = "Not Set";
    var term_n_week = getTermWeek(dates_details, date);
    for (let index = 0; index < medium_term_plan.length; index++) {
        const element = medium_term_plan[index];
        if (element.term_name == term_n_week[0] && element.week_name == term_n_week[1]) {
            sub_strand = element.sub_strand == undefined ? "Not Set" : element.sub_strand;
        }
    }

    // set the innertext with the data
    cObj("subtopic_associated").innerText = sub_strand;

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

    cObj("topic_associated").innerText = topic_set;
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