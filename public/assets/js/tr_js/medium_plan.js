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


// select the different term
cObj("select_term").onchange = function () {
    var this_value = this.value;
    if (this_value == "Term 1") {
        cObj("term_select_weeks1").classList.remove("hide");
        cObj("term_select_weeks2").classList.add("hide");
        cObj("term_select_weeks3").classList.add("hide");
    }else if (this_value == "Term 2") {
        cObj("term_select_weeks1").classList.add("hide");
        cObj("term_select_weeks2").classList.remove("hide");
        cObj("term_select_weeks3").classList.add("hide");
    }else if (this_value == "Term 3") {
        cObj("term_select_weeks1").classList.add("hide");
        cObj("term_select_weeks2").classList.add("hide");
        cObj("term_select_weeks3").classList.remove("hide");
    }else{
        cObj("term_select_weeks1").classList.remove("hide");
        cObj("term_select_weeks2").classList.add("hide");
        cObj("term_select_weeks3").classList.add("hide");
    }
    cObj("term_one_default").selected = true;
    cObj("term_two_default").selected = true;
    cObj("term_three_default").selected = true;
    displayData();
}
cObj("select_week_1").onchange = function () {
    displayData();
}
cObj("select_week_2").onchange = function () {
    displayData();
}
cObj("select_week_3").onchange = function () {
    displayData();
}
window.onload = function () {
    displayData();
}
cObj("readonly_mode").onchange = function () {
    displayData();
}
function displayData() {
    var selected_term = valObj("select_term");
    var week = 1;
    var total_weeks = valObj("term_one_weeks");
    if (selected_term.length > 0 && total_weeks.length > 0) {
        if (selected_term == "Term 1") {
            week = valObj("select_week_1");
            total_weeks = valObj("term_one_weeks");
        }else if (selected_term == "Term 2") {
            week = valObj("select_week_2");
            total_weeks = valObj("term_two_weeks");
        }else if (selected_term == "Term 3") {
            week = valObj("select_week_3");
            total_weeks = valObj("term_three_weeks");
        }else{
            week = valObj("select_week_1");
            total_weeks = valObj("term_one_weeks");
        }
        
        var hold_data = document.getElementsByClassName("hold_data");
        var hold_data_origin = document.getElementsByClassName("hold_data_origin");

        // get the data
        var unsimilar = 0;
        for (let index = 0; index < hold_data.length; index++) {
            const element = hold_data[index];
            var element_id = element.id;
            if (element.value != cObj("hold_data_origin_"+element_id.substring(10)).value) {
                unsimilar++;
            }
        }
        if (unsimilar > 0) {
            var confirmationMessage = 'Your changes won`t be saved, Are you sure you want to leave?';
            if (!window.confirm(confirmationMessage)) {
                // get the selected
              // User clicked "No"
            //   e.returnValue = confirmationMessage; // For Safari
              return confirmationMessage; // For other browsers
            }
        }
    
        displayMediumPLan(lesson_plan,week,selected_term,total_weeks);
    }
}
function displayMediumPLan(data,week,term,total_weeks) {
    var delete_status = cObj("readonly_mode").checked == true ? "hide":"";
    var tooltip = cObj("readonly_mode").checked == true ? "data-bs-toggle='tooltip' data-toggle='tooltip' data-bs-placement='top' title='Toggle the readonly button to Off so you can edit this.'" : "";
    var disable_sub_strand_select = cObj("readonly_mode").checked == true ? "disabled" : "";
    data = JSON.parse(data);
    // get the data for the three consecutive weeks
    // console.log(term);
    var weeks_data = [];
    if (((week*1) +2) <= total_weeks) {
        // this tells us that from the current week selected we have more weeks ahead from the total
        // 2+2 = 4 and we have 17 weeks its true
        var max_week = (week*1) +2;
        for (let index = week; index <= (max_week); index++) {
            var one_Week_data = {
                objectives:[],
                resources:[],
                activities:[],
                completed:false,
                week_name:index,
                term_name:term,
                comments:"",
                sub_strand:""
            };
            for (let ind = 0; ind < data.length; ind++) {
                const element = data[ind];
                if (element.term_name == term && element.week_name == index) {
                    one_Week_data = element;
                    break;
                }
            }

            weeks_data.push(one_Week_data);
        }
    }else{
        // the weeks are less
        for (let index = week; index <= total_weeks; index++) {
            var one_Week_data = {
                objectives:[],
                resources:[],
                activities:[],
                completed:false,
                week_name:index,
                term_name:term,
                comments:"",
                sub_strand:""
            };
            for (let ind = 0; ind < data.length; ind++) {
                const element = data[ind];
                if (element.term_name == term && element.week_name == index) {
                    one_Week_data = element;
                    break;
                }
            }
            weeks_data.push(one_Week_data);
        }
    }
    
    // go with the data and display the three windows
    var data_to_display = "";

    for (let index = 0; index < weeks_data.length; index++) {
        const element = weeks_data[index];

        // get selected week
        var selected_status = "";
        if (element.week_name == week) {
            selected_status="<div class='container border border-primary shadow-lg border-3 p-2 rounded'>";
        }else{
            selected_status="<div class='container border border-secondary shadow-lg p-2 rounded'>";
        }

        // display objectives
        var objectives = "";
        if (element.objectives.length > 0) {
            for (let ind = 0; ind < element.objectives.length; ind++) {
                const elem = element.objectives[ind];
                objectives+="<li class='list-group-item'>"+(ind+1)+". "+elem.value+" <input type='hidden' value = '"+elem.id+"' id='objective_id_"+elem.id+"_"+index+"'> <span style='cursor:pointer;' class='text-danger trash_objective "+delete_status+"' id = 'trash_objective_"+elem.id+"_"+index+"'><i class='bi bi-trash'></i></span></li>";
            }
        }else{
            objectives+="<h3 class='text-center text-secondary mt-1'><i class='bi bi-exclamation-triangle'></i></h3><p class='text-secondary text-center'>No objectives set for Week "+element.week_name+"!</p>";
        }

        // display Resources
        var resources = "";
        if (element.resources.length > 0) {
            for (let ind = 0; ind < element.resources.length; ind++) {
                const elem = element.resources[ind];
                resources+="<li class='list-group-item'>"+(ind+1)+". "+elem.value+" <input type='hidden' value = '"+elem.id+"' id='resource_id_"+elem.id+"_"+index+"'> <span style='cursor:pointer;' class='text-danger trash_resources "+delete_status+"' id = 'trash_resources_"+elem.id+"_"+index+"'><i class='bi bi-trash'></i></span></li>";
            }
        }else{
            resources+="<h3 class='text-center text-secondary mt-1'><i class='bi bi-exclamation-triangle'></i></h3><p class='text-secondary text-center'>No resources set for Week "+element.week_name+"!</p>";
        }

        // display activities
        var activities = "";
        if (element.activities.length > 0) {
            for (let ind = 0; ind < element.activities.length; ind++) {
                const elem = element.activities[ind];
                activities+="<li class='list-group-item'>"+(ind+1)+". "+elem.value+" <input type='hidden' value = '"+elem.id+"' id='activities_id_"+elem.id+"_"+index+"'> <span style='cursor:pointer;' class='text-danger trash_activities "+delete_status+"' id = 'trash_activities_"+elem.id+"_"+index+"'><i class='bi bi-trash'></i></span></li>";
            }
        }else{
            activities+="<h3 class='text-center text-secondary mt-1'><i class='bi bi-exclamation-triangle'></i></h3><p class='text-secondary text-center'>No activities set for Week "+element.week_name+"!</p>";
        }
        
        // comments 
        var week_comments = element.comments.length > 0 ? element.comments : "No Comments Set!";
        var our_dates = getDates(term,element.week_name);
        var strand_subs_strands = getStrandSubstrands(strands_data,element.week_name,term);
        var select_substrand = "<select "+disable_sub_strand_select+" class='form-control select_substrands' id='select_substrands_"+index+"' "+tooltip+" ><option hidden>Select Sub-Strand / Sub-Topic</option>";
        for (let index = 0; index < strand_subs_strands[1].length; index++) {
            const elems = strand_subs_strands[1][index];
            var select_strand = element.sub_strand == elems.substrand_name ? "selected" : "";
            select_substrand+="<option "+select_strand+" value='"+elems.substrand_name+"' >"+elems.substrand_name+" : "+elems.substrand_code+"</option>";
        }
        select_substrand+="</select>";
        var display_data = "<div class='col-lg-4 my-1'>"+
                            // if week is the selected one add the selected feature border feature
                            selected_status+
                            // ends here
                                    "<h6 class='bg-white p-2 text-center'>"+term+" : Week #"+element.week_name+" </h6>"+
                                    "<div><button type='button' class='btn btn-outline-success my-1 week_details_btn' id='week_details_btn_"+index+"' data-bs-toggle='tooltip' data-bs-placement='top' title='Click to view Week #"+element.week_name+" Details'><i class='bi bi-exclamation-octagon'></i></button></div>"+
                                    "<div class ='border border-primary rounded p-1 hide' id='week_details_"+index+"'><p class='text-center'><b>Week "+element.week_name+" Details</b></p>"+
                                    "<p class='bg-white p-2 text-left my-0'><b>From: </b>"+our_dates[0]+" <b>,To:</b> "+our_dates[1]+"</p>"+
                                    "<p class='bg-white p-2 my-0 text-left'><b>Strand / Topic Associated: </b>"+strand_subs_strands[0]+"</p>"+
                                    "<label class='bg-white p-2 my-0 text-left form-label'><b>Sub-Strand / Sub-Topic Associated: </b></label>"+
                                    select_substrand+
                                    "</div>"+
                                    "<label for='week_objective_"+index+"' class='form-label'><b>Week #"+element.week_name+" Objective</b>  <button id='btn_week_"+index+"' class='btn btn-outline-primary btn-sm btn_week "+delete_status+"'><i class='bi bi-plus'></i> Add</button></label>"+
                                    "<div class='hide border border-success rounded p-1' id='week_objective_window_"+index+"'>"+
                                        "<label for='week_objective_"+index+"' class='form-label'>Week #"+element.week_name+" Add Objectives</label>"+
                                        "<input type='hidden' class='form-control' id='week_n_term_"+index+"' value='[\""+element.week_name+"\",\""+term+"\"]'>"+
                                        "<div class='autocomplete'><input type='text' class='form-control week_objective_input' id='week_objective_"+index+"' placeholder='Students should be able to...'></div>"+
                                        "<button class='btn btn-primary my-1 week_objective_btn' id='btn_save_obj_"+index+"'><i class='bi bi-plus'></i> Add</button>"+
                                    "</div>"+
                                    "<ul class='list-group' id='display_objectives_"+index+"'>"+
                                        objectives+
                                    "</ul>"+
                                    "<hr>"+
                                    "<label for='week_resources_"+index+"' class='form-label'><b>Week #"+element.week_name+" Resources</b>  <button id='btn_resource_wk_"+index+"' class='btn btn-outline-primary btn-sm btn_resource_wk "+delete_status+"'><i class='bi bi-plus'></i> Add</button></label>"+
                                    "<div class='hide border border-success rounded p-1' id='resources_week_"+index+"'>"+
                                        "<label for='week_resources_"+index+"' class='form-label'>Week #"+element.week_name+" Add Resources</label>"+
                                        "<div class='autocomplete'><input type='text' class='form-control week_resources_input' id='week_resources_"+index+"' placeholder='KLB Pg 1-11...'></div>"+
                                        "<button class='btn btn-primary my-1 btn_save_res' id='btn_save_res_"+index+"'><i class='bi bi-plus'></i> Add</button>"+
                                    "</div>"+
                                    "<ul class='list-group' id='display_resources_"+index+"'>"+
                                        resources+
                                    "</ul>"+
                                    "<hr>"+
                                    "<label for='week_activities_"+index+"' class='form-label'><b>Week #"+element.week_name+" Activities</b>  <button id='btn_activity_wk_"+index+"' class='btn btn-outline-primary btn-sm btn_activity_wk "+delete_status+"'><i class='bi bi-plus'></i> Add</button></label>"+
                                    "<div class='hide border border-success rounded p-1' id='activity_week_"+index+"'>"+
                                        "<label for='week_activities_"+index+"' class='form-label'>Week #"+element.week_name+" Add Activities</label>"+
                                        "<input type='text' class='form-control week_activities_input' id='week_activities_"+index+"' placeholder='Creation of visual aids...'>"+
                                        "<button class='btn btn-primary my-1 btn_save_act' id='btn_save_act_"+index+"'><i class='bi bi-plus'></i> Add</button>"+
                                    "</div>"+
                                    "<ul class='list-group' id='display_activities_"+index+"'>"+
                                        activities+
                                    "</ul>"+
                                    "<hr>"+
                                    "<label for='week_comments_"+index+"' class='form-label'><b>Week "+element.week_name+" Comment:</b>  <button id='btn_comments_wk_"+index+"' class='btn btn-outline-primary btn-sm btn_comments_wk "+delete_status+"'><i class='bi bi-plus'></i> Add</button></label>"+
                                    "<div class='hide border border-success rounded p-1' id='comments_week_"+index+"'>"+
                                        "<label for='week_comments_"+index+"' class='form-label'>Week "+element.week_name+": Add Comments</label>"+
                                        "<textarea name='week_comments_"+index+"' id='week_comments_"+index+"' cols='30' rows='5' class='form-control' placeholder='Comment here..'></textarea>"+
                                        "<button class='btn btn-primary my-1 btn_save_comments' id='btn_save_comments_"+index+"'><i class='bi bi-save'></i> Save</button>"+
                                    "</div>"+
                                        "<p class='text-sm mt-1 p-1 bg-white border border-primary rounded' id='display_comments_"+index+"'>"+week_comments+"</p>"+
                                    "<form action='/updateMediumPlan' method='post' class='container my-2 p-0'>"+
                                        "<input type='hidden' name='subject_id' value='"+lesson_id_medium+"'>"+
                                        "<input type='hidden' name='subject_class' value='"+class_medium+"'>"+
                                        "<input type='hidden' class='hold_data' name='hold_data' id='hold_data_"+index+"' value='"+JSON.stringify(element)+"'>"+
                                        "<input type='hidden' class='hold_data_origin' name='hold_data_origin' id='hold_data_origin_"+index+"' value='"+JSON.stringify(element)+"'>"+
                                        "<button type='submit' class='btn btn-primary btn-block w-100 "+delete_status+"'><i class='bi bi-save'></i> Save</button>"+
                                    "</form>"+
                                "</div>"+
                            "</div>";
                            data_to_display+=display_data;
    }
    cObj("medium_plan_data").innerHTML = data_to_display;

    // SET LISTENERS FOR THIS WINDOW
    var btn_week = document.getElementsByClassName("btn_week");
    for (let index = 0; index < btn_week.length; index++) {
        const element = btn_week[index];
        element.addEventListener("click",displayObjectiveWindow);
    }

    var btn_resource_wk = document.getElementsByClassName("btn_resource_wk");
    for (let index = 0; index < btn_resource_wk.length; index++) {
        const element = btn_resource_wk[index];
        element.addEventListener("click",displayResourceWindow);
    }

    var btn_activity_wk = document.getElementsByClassName("btn_activity_wk");
    for (let index = 0; index < btn_activity_wk.length; index++) {
        const element = btn_activity_wk[index];
        element.addEventListener("click",displayActivityWindow);
    }

    var btn_comments_wk = document.getElementsByClassName("btn_comments_wk");
    for (let index = 0; index < btn_comments_wk.length; index++) {
        const element = btn_comments_wk[index];
        element.addEventListener("click",displayCommentWindow);
    }

    // buttons to save objectives resources activities and comments
    var week_objective_btn = document.getElementsByClassName("week_objective_btn");
    for (let index = 0; index < week_objective_btn.length; index++) {
        const element = week_objective_btn[index];
        element.addEventListener("click",saveObjectives);
    }

    var btn_save_res = document.getElementsByClassName("btn_save_res");
    for (let index = 0; index < btn_save_res.length; index++) {
        const element = btn_save_res[index];
        element.addEventListener("click",saveResources);
    }

    var btn_save_act = document.getElementsByClassName("btn_save_act");
    for (let index = 0; index < btn_save_act.length; index++) {
        const element = btn_save_act[index];
        element.addEventListener("click",saveActivities);
    }

    var btn_save_comments = document.getElementsByClassName("btn_save_comments");
    for (let index = 0; index < btn_save_comments.length; index++) {
        const element = btn_save_comments[index];
        element.addEventListener("click",saveComments);
    }

    // add listener to the removers
    var trash_objective = document.getElementsByClassName("trash_objective");
    for (let index = 0; index < trash_objective.length; index++) {
        const element = trash_objective[index];
        element.addEventListener("click",trashObjectives);
        // console.log(element.id);
    }

    var trash_resources = document.getElementsByClassName("trash_resources");
    for (let index = 0; index < trash_resources.length; index++) {
        const element = trash_resources[index];
        element.addEventListener("click",trashResources);
    }

    var trash_activities = document.getElementsByClassName("trash_activities");
    for (let index = 0; index < trash_activities.length; index++) {
        const element = trash_activities[index];
        element.addEventListener("click",trashActivities);
    }

    // when the enter key is pressed in either of the input boxes it should equate to the add button being pressed
    var week_objective_input = document.getElementsByClassName("week_objective_input");
    for (let index = 0; index < week_objective_input.length; index++) {
        const element = week_objective_input[index];
        element.addEventListener("keyup",function (event) {
            if (event.key === "Enter") {
                var ths_id = this.id.substr(15);
                cObj("btn_save_obj_"+ths_id).click();
              }
        });
        var term_week = hasJsonStructure(valObj("week_n_term_"+element.id.substring(15))) ? JSON.parse(valObj("week_n_term_"+element.id.substring(15))):["1","Term 1"];
        var role = "objectives";
        var objectives = getPopulators(populators,term_week[0],term_week[1],role);
        // set autocomplete
        autocomplete(element,objectives);
    }

    var week_resources_input = document.getElementsByClassName("week_resources_input");
    for (let index = 0; index < week_resources_input.length; index++) {
        const element = week_resources_input[index];
        element.addEventListener("keyup",function (event) {
            if (event.key === "Enter") {
                var ths_id = this.id.substr(15);
                cObj("btn_save_res_"+ths_id).click();
              }
        });
        var term_week = hasJsonStructure(valObj("week_n_term_"+element.id.substring(15))) ? JSON.parse(valObj("week_n_term_"+element.id.substring(15))):["1","Term 1"];
        var role = "resources";
        var resources = getPopulators(populators,term_week[0],term_week[1],role);
        // set autocomplete
        autocomplete(element,resources);
    }

    var week_activities_input = document.getElementsByClassName("week_activities_input");
    for (let index = 0; index < week_activities_input.length; index++) {
        const element = week_activities_input[index];
        element.addEventListener("keyup",function (event) {
            if (event.key === "Enter") {
                var ths_id = this.id.substr(16);
                cObj("btn_save_act_"+ths_id).click();
              }
        });
    }

    var week_details_btn = document.getElementsByClassName("week_details_btn");
    for (let index = 0; index < week_details_btn.length; index++) {
        const element = week_details_btn[index];
        element.addEventListener("click",function (e) {
            var this_id = this.id.substr(17);
            cObj("week_details_"+this_id).classList.toggle("hide");
        });
    }
    var select_substrands = document.getElementsByClassName("select_substrands");
    for (let index = 0; index < select_substrands.length; index++) {
        const element = select_substrands[index];
        element.addEventListener("change",function () {
            var this_value = this.value;
            var id = this.id.substr(18);

            var obj_value = this_value;
    
            // add it to the rest of the list
            var hold_data = valObj("hold_data_"+id);
            if (hasJsonStructure(hold_data)) {
                hold_data = JSON.parse(hold_data);
                hold_data.sub_strand = obj_value;
    
                cObj("hold_data_"+id).value = JSON.stringify(hold_data);
            }
            displayHoldData(id);
        });
    }
    /**
     * Initiate tooltips
     */
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })
}

function saveComments() {
    var id = this.id.substr(18);

    // check for blank errors in the input fields
    var err = checkBlank("week_comments_"+id);
    if (err == 0) {
        var obj_value = valObj("week_comments_"+id);

        // add it to the rest of the list
        var hold_data = valObj("hold_data_"+id);
        if (hasJsonStructure(hold_data)) {
            hold_data = JSON.parse(hold_data);
            hold_data.comments = obj_value;

            cObj("hold_data_"+id).value = JSON.stringify(hold_data);
        }

        // empty the input field
        cObj("week_comments_"+id).value = "";
    }
    displayHoldData(id);
}
function saveActivities() {
    var id = this.id.substr(13);

    // check for blank errors in the input fields
    var err = checkBlank("week_activities_"+id);
    if (err == 0) {
        var obj_value = valObj("week_activities_"+id);

        // add it to the rest of the list
        var hold_data = valObj("hold_data_"+id);
        if (hasJsonStructure(hold_data)) {
            hold_data = JSON.parse(hold_data);
            var activities = hold_data.activities;
            var next_id = (activities.length>0) ? (activities[activities.length-1].id * 1)+1 : 1;
            var new_data = {id:next_id,value:obj_value};
            hold_data.activities.push(new_data);

            cObj("hold_data_"+id).value = JSON.stringify(hold_data);
        }

        // empty the input field
        cObj("week_activities_"+id).value = "";
    }
    displayHoldData(id);
}

function saveResources() {
    var id = this.id.substr(13);

    // check for blank errors in the input fields
    var err = checkBlank("week_resources_"+id);
    if (err == 0) {
        var obj_value = valObj("week_resources_"+id);

        // add it to the rest of the list
        var hold_data = valObj("hold_data_"+id);
        if (hasJsonStructure(hold_data)) {
            hold_data = JSON.parse(hold_data);
            var resources = hold_data.resources;
            var next_id = (resources.length>0) ? (resources[resources.length-1].id * 1)+1 : 1;
            var new_data = {id:next_id,value:obj_value};
            hold_data.resources.push(new_data);

            cObj("hold_data_"+id).value = JSON.stringify(hold_data);
        }

        // empty the input field
        cObj("week_resources_"+id).value = "";
    }
    displayHoldData(id);
}

function saveObjectives() {
    var id = this.id.substr(13);

    // check for blank errors in the input fields
    var err = checkBlank("week_objective_"+id);
    if (err == 0) {
        var obj_value = valObj("week_objective_"+id);

        // add it to the rest of the list
        var hold_data = valObj("hold_data_"+id);
        if (hasJsonStructure(hold_data)) {
            hold_data = JSON.parse(hold_data);
            var objectives = hold_data.objectives;
            var next_id = (objectives.length>0) ? (objectives[objectives.length-1].id * 1)+1 : 1;
            var new_data = {id:next_id,value:obj_value};
            hold_data.objectives.push(new_data);

            cObj("hold_data_"+id).value = JSON.stringify(hold_data);
        }

        // empty the input field
        cObj("week_objective_"+id).value = "";
    }
    displayHoldData(id);
}

function displayHoldData(id) {
    // var bid = {
    //     objectives:[],
    //     resources:[],
    //     activities:[],
    //     completed:false,
    //     week_name:index,
    //     term_name:term,
    //     comments:""
    // };
    var delete_status = cObj("readonly_mode").checked == true ? "hide":"";
    var hold_data = cObj("hold_data_"+id).value;
    if (hasJsonStructure(hold_data)) {
        hold_data = JSON.parse(hold_data);
        

        // hold objectives
        var objectives = hold_data.objectives;
        var display_data_objectives = "";
        if (objectives.length > 0) {
            for (let index = 0; index < objectives.length; index++) {
                const element = objectives[index];
                display_data_objectives+="<li class='list-group-item'>"+(index+1)+". "+element.value+" <input type='hidden' value = '"+element.id+"' id='objective_id_"+element.id+"_"+id+"'> <span style='cursor:pointer;' class='text-danger trash_objective "+delete_status+"' id = 'trash_objective_"+element.id+"_"+id+"'><i class='bi bi-trash'></i></span></li>";
            }
        }else{
            display_data_objectives+="<h3 class='text-center text-secondary mt-1'><i class='bi bi-exclamation-triangle'></i></h3><p class='text-secondary text-center'>No objectives set for Week "+hold_data.week_name+"!</p>";
        }
        cObj("display_objectives_"+id).innerHTML = display_data_objectives;

        // hold resources
        var resources = hold_data.resources;
        var display_data_resources = "";
        if (resources.length > 0) {
            for (let index = 0; index < resources.length; index++) {
                const element = resources[index];
                display_data_resources+="<li class='list-group-item'>"+(index+1)+". "+element.value+" <input type='hidden' value = '"+element.id+"' id='resource_id_"+element.id+"_"+id+"'> <span style='cursor:pointer;' class='text-danger trash_resources "+delete_status+"' id = 'trash_resources_"+element.id+"_"+id+"'><i class='bi bi-trash'></i></span></li>";
            }
        }else{
            display_data_resources+="<h3 class='text-center text-secondary mt-1'><i class='bi bi-exclamation-triangle'></i></h3><p class='text-secondary text-center'>No resources set for Week "+hold_data.week_name+"!</p>";
        }
        cObj("display_resources_"+id).innerHTML = display_data_resources;

        // hold resources
        var activities = hold_data.activities;
        var display_data_activities = "";
        if (activities.length > 0) {
            for (let index = 0; index < activities.length; index++) {
                const element = activities[index];
                display_data_activities+="<li class='list-group-item'>"+(index+1)+". "+element.value+" <input type='hidden' value = '"+element.id+"' id='activities_id_"+element.id+"_"+id+"'> <span style='cursor:pointer;' class='text-danger trash_activities "+delete_status+"' id = 'trash_activities_"+element.id+"_"+id+"'><i class='bi bi-trash'></i></span></li>";
            }
        }else{
            display_data_activities+="<h3 class='text-center text-secondary mt-1'><i class='bi bi-exclamation-triangle'></i></h3><p class='text-secondary text-center'>No activities set for Week "+hold_data.week_name+"!</p>";
        }
        cObj("display_activities_"+id).innerHTML = display_data_activities;

        cObj("display_comments_"+id).innerText = hold_data.comments.trim().length > 0 ? hold_data.comments : "No Comments Set!";
    }

    // add listener to the removers
    var trash_objective = document.getElementsByClassName("trash_objective");
    for (let index = 0; index < trash_objective.length; index++) {
        const element = trash_objective[index];
        element.addEventListener("click",trashObjectives);
    }

    var trash_resources = document.getElementsByClassName("trash_resources");
    for (let index = 0; index < trash_resources.length; index++) {
        const element = trash_resources[index];
        element.addEventListener("click",trashResources);
    }

    var trash_activities = document.getElementsByClassName("trash_activities");
    for (let index = 0; index < trash_activities.length; index++) {
        const element = trash_activities[index];
        element.addEventListener("click",trashActivities);
    }
}

function trashActivities() {
    var this_ids = this.id.substr(17);
    var obj_index = cObj("activities_id_"+this_ids).value;
    // get the week index
    var week_index = this_ids.substr((obj_index+"").length + 1);
    
    // get the hold data
    var hold_data = cObj("hold_data_"+week_index).value;

    if (hasJsonStructure(hold_data)) {
        hold_data = JSON.parse(hold_data);
        var activities = hold_data.activities;
        var new_activities = [];
        for (let index = 0; index < activities.length; index++) {
            const element = activities[index];
            if (element.id == obj_index) {
                continue;
            }
            new_activities.push(element);
        }
        hold_data.activities = new_activities;
        cObj("hold_data_"+week_index).value = JSON.stringify(hold_data);
    }
    displayHoldData(week_index);
}

function trashResources() {
    var this_ids = this.id.substr(16);
    var obj_index = cObj("resource_id_"+this_ids).value;
    // get the week index
    var week_index = this_ids.substr((obj_index+"").length + 1);
    
    // get the hold data
    var hold_data = cObj("hold_data_"+week_index).value;

    if (hasJsonStructure(hold_data)) {
        hold_data = JSON.parse(hold_data);
        var resources = hold_data.resources;
        var new_resources = [];
        for (let index = 0; index < resources.length; index++) {
            const element = resources[index];
            if (element.id == obj_index) {
                continue;
            }
            new_resources.push(element);
        }
        hold_data.resources = new_resources;
        cObj("hold_data_"+week_index).value = JSON.stringify(hold_data);
    }
    displayHoldData(week_index);
}

function trashObjectives() {
    var this_ids = this.id.substr(16);
    var obj_index = cObj("objective_id_"+this_ids).value;
    // get the week index
    var week_index = this_ids.substr((obj_index+"").length + 1);

    // get the hold data
    var hold_data = cObj("hold_data_"+week_index).value;
    // console.log(hold_data);

    if (hasJsonStructure(hold_data)) {
        hold_data = JSON.parse(hold_data);
        var objectives = hold_data.objectives;
        var new_objectives = [];
        for (let index = 0; index < objectives.length; index++) {
            const element = objectives[index];
            if (element.id == obj_index) {
                continue;
            }
            new_objectives.push(element);
        }
        hold_data.objectives = new_objectives;
        cObj("hold_data_"+week_index).value = JSON.stringify(hold_data);
    }
    displayHoldData(week_index);
}

function displayObjectiveWindow() {
    var id = this.id.substr(9);
    cObj("week_objective_window_"+id).classList.toggle("hide");
}

function displayResourceWindow() {
    var id = this.id.substr(16);
    cObj("resources_week_"+id).classList.toggle("hide");
}

function displayActivityWindow() {
    var id = this.id.substr(16);
    cObj("activity_week_"+id).classList.toggle("hide");
}

function displayCommentWindow() {
    var id = this.id.substr(16);
    cObj("comments_week_"+id).classList.toggle("hide");
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

// this function returns the objective or resources
function getPopulators(data,week,term,role = 'objectives') {
    var objectives = [];
    var resources = [];

    for (let index = 0; index < data.length; index++) {
        const element = data[index];
        if (element.term == term) {
            var week_btwn = element.week.split("-");
            if (week >= week_btwn[0]*1 && week <= week_btwn[1]*1) {
                objectives = element.objectives;
                resources = element.resources;
            }
        }
    }

    if (role == "objectives") {
        return objectives;
    }else{
        return resources;
    }
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

function getDates(term,week) {
    for (let index = 0; index < dates_details.length; index++) {
        const element = dates_details[index];
        if (element.week == week && element.term == term) {
            return [element.date_start,element.date_end];
        }
    }
    return ["Not Set","Not Set"];
}

// this function returns the objective or resources
function getStrandSubstrands(data,week,term) {
    var strand_name = "Not Set";
    var substrands = [];
    week*=1;

    for (let index = 0; index < data.length; index++) {
        const element = data[index];
        if (element.term == term) {
            var week_btwn = element.weeks.split("-");
            if (week >= week_btwn[0]*1 && week <= week_btwn[1]*1) {
                strand_name = element.strand_name;
                substrands = element.substrands;
            }
        }
    }
    return [strand_name,substrands];
}