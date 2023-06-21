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

cObj("add_objective_window").addEventListener("click",showAddObjectives);

function showAddObjectives() {
    cObj("objective_record_window").classList.toggle("hide");
}

// record all the strands objectives
cObj("add_objective").onclick = function () {
    var err = checkBlank("strands_objectives");
    if (err == 0) {
        if (valObj("strands_objectives_holder").length > 0) {
            var stored_objectives = JSON.parse(valObj("strands_objectives_holder"));
            stored_objectives.push(valObj("strands_objectives"));
            cObj("strands_objectives_holder").value = JSON.stringify(stored_objectives);
        }else{
            var objectives = [];
            objectives.push(valObj("strands_objectives"));
            cObj("strands_objectives_holder").value = JSON.stringify(objectives);
        }
    }
    cObj("strands_objectives").value = "";
    displayStrandObjectives();
}
function displayStrandObjectives() {
    if (valObj("strands_objectives_holder").length > 0) {
        var stored_objectives = JSON.parse(valObj("strands_objectives_holder"));
        if (stored_objectives.length > 0) {
            var data_to_display = "";
            for (let index = 0; index < stored_objectives.length; index++) {
                const element = stored_objectives[index];
                data_to_display+="<li class='list-group-item'>"+element+"  <span style='cursor:pointer;' class='text-danger trash_objectives' id='t_obj_"+index+"'><i class='bi bi-trash'></i></span></li>";
            }
            cObj("strands_obj_list").innerHTML = data_to_display;
        }else{
            cObj("strands_obj_list").innerHTML = "<li class='list-group-item text-danger'>No lists available at the moment  <a href='#' class='text-danger'><i class='bi bi-trash'></i></a></li>";
        }
    }else{
        cObj("strands_obj_list").innerHTML = "<li class='list-group-item text-black'>No lists available at the moment.</li>";
    }
    deleteObjectiveFromList();
}

function deleteObjectiveFromList() {
    // set the delete action to the delete button
    var trash_objectives = document.getElementsByClassName("trash_objectives");
    for (let index = 0; index < trash_objectives.length; index++) {
        const element = trash_objectives[index];
        element.addEventListener("click",deleteObjective);
    }
}

function deleteObjective() {
    var this_id = this.id.substr(6);
    var stored_objectives = valObj("strands_objectives_holder");
    
    if (stored_objectives.length > 0) {
        stored_objectives = JSON.parse(stored_objectives);
        var new_objectives = [];
        for (let index = 0; index < stored_objectives.length; index++) {
            const element = stored_objectives[index];
            if (index == this_id) {
                continue;
            }
            new_objectives.push(element);
        }
        if (new_objectives.length > 0) {
            cObj("strands_objectives_holder").value = JSON.stringify(new_objectives);
        }else{
            cObj("strands_objectives_holder").value = "";
        }
    }
    displayStrandObjectives();
}

cObj("add_learning_material").onclick = function () {
    cObj("add_learning_materials_window").classList.toggle("hide");
}

cObj("add_learning_materials_list").onclick = function () {
    var err  = checkBlank("learning_materials");
    if (err == 0) {
        var lm_list = valObj("learning_materials_holder");
        if (lm_list.length > 0) {
            lm_list = JSON.parse(lm_list);
            lm_list.push(valObj("learning_materials"));
            
            cObj("learning_materials_holder").value = JSON.stringify(lm_list);
        }else{
            var lms_list = [];
            lms_list.push(valObj("learning_materials"));
            cObj("learning_materials_holder").value = JSON.stringify(lms_list);
        }
    }
    cObj("learning_materials").value = "";
    displayLM();
}

function displayLM() {
    var learning_materials = valObj("learning_materials_holder");
    if (learning_materials.length > 0) {
        learning_materials = JSON.parse(learning_materials);
        var data_to_display = "";
        for (let index = 0; index < learning_materials.length; index++) {
            const element = learning_materials[index];
            data_to_display += "<li class='list-group-item'>"+element+"  <span style='cursor:pointer;' class='text-danger trash_LM' id='t_LM_"+index+"'><i class='bi bi-trash'></i></span></li>";
        }
        cObj("learning_materials_lists").innerHTML = data_to_display;
    }else{
        cObj("learning_materials_lists").innerHTML = "<li class='list-group-item text-black'>No lists available at the moment.</li>";
    }

    deleteLearningMaterial();
}

function deleteLearningMaterial() {
    var trash_LM = document.getElementsByClassName("trash_LM");
    for (let index = 0; index < trash_LM.length; index++) {
        const element = trash_LM[index];
        element.addEventListener("click",deleteLearnMaterial);
    }
}

function deleteLearnMaterial() {
    var this_ids = this.id.substr(5);
    var learning_materials_holder = valObj("learning_materials_holder");
    if (learning_materials_holder.length > 0) {
        learning_materials_holder = JSON.parse(learning_materials_holder);
        var new_lm_list = [];
        for (let index = 0; index < learning_materials_holder.length; index++) {
            const element = learning_materials_holder[index];
            if (index == this_ids) {
                continue;
            }
            new_lm_list.push(element);
        }

        if (new_lm_list.length > 0) {
            cObj("learning_materials_holder").value = JSON.stringify(new_lm_list);
        }else{
            cObj("learning_materials_holder").value = "";
        }
    }
    displayLM();
}

// toogle the descriptions for the substrands
var add_objective_window = document.getElementsByClassName("add_objective_window");
for (let index = 0; index < add_objective_window.length; index++) {
    const element = add_objective_window[index];
    element.addEventListener("click",showObjectives);
}

function showObjectives() {
    var ids = this.id.substr(21);
    cObj("objective_record_window_"+ids).classList.toggle("hide");
}

// show and hide learning material window
var add_learning_materials = document.getElementsByClassName("add_learning_materials");
for (let index = 0; index < add_learning_materials.length; index++) {
    const element = add_learning_materials[index];
    element.addEventListener("click",showLearningMaterials);
}

function showLearningMaterials() {
    var this_id = this.id.substr(23);
    cObj("add_learning_materials_window_"+this_id).classList.toggle("hide");
}

var add_learning_materials_list = document.getElementsByClassName("add_learning_materials_list");
for (let index = 0; index < add_learning_materials_list.length; index++) {
    const element = add_learning_materials_list[index];
    element.addEventListener("click",addLearningMaterial);
}

function addLearningMaterial() {
    var this_id = this.id.substr(27);
    var err = checkBlank("learning_materials"+this_id);
    if (err == 0) {
        var data_held = valObj("learning_materials_holder"+this_id);
        if (data_held.length > 0) {
            var data = JSON.parse(data_held);
            data.push(valObj("learning_materials"+this_id));
            cObj("learning_materials_holder"+this_id).value = JSON.stringify(data);
        }else{
            var data = [];
            data.push(valObj("learning_materials"+this_id));
            cObj("learning_materials_holder"+this_id).value = JSON.stringify(data);
        }
    }
    cObj("learning_materials"+this_id).value = "";

    displayLMsub(this_id);
}

function displayLMsub(this_id) {
    var learning_materials = valObj("learning_materials_holder"+this_id);
    if (learning_materials.length > 0) {
        if (hasJsonStructure(learning_materials)) {
            learning_materials = JSON.parse(learning_materials);
            if (learning_materials.length > 0) {
                var data_to_display = "";
                for (let index = 0; index < learning_materials.length; index++) {
                    const element = learning_materials[index];
                    data_to_display+="<li class='list-group-item'>"+element+"  <span style='cursor:pointer;' class='text-danger trash_LM_"+this_id+"' id='t_LM_"+this_id+"_"+index+"'><i class='bi bi-trash'></i></span></li>";
                }
                // console.log(learning_materials);
                cObj("learning_materials_lists"+this_id).innerHTML = data_to_display;
            }else{
                cObj("learning_materials_lists"+this_id).innerHTML = "<li class='list-group-item text-black'>No learning materials available at the moment.</li>";
            }
        }else{
            cObj("learning_materials_lists"+this_id).innerHTML = "<li class='list-group-item text-black'>No learning materials available at the moment.</li>";
        }
    }else{
        cObj("learning_materials_lists"+this_id).innerHTML = "<li class='list-group-item text-black'>No learning materials available at the moment.</li>";
    }

    var delete_windows = document.getElementsByClassName("trash_LM_"+this_id);
    for (let index = 0; index < delete_windows.length; index++) {
        const element = delete_windows[index];
        element.addEventListener("click",deleteLearningMaterials);
    }
}

function deleteLearningMaterials() {
    var id = this.id;
    var class_id = this.className.split(" ")[1].substr(9);
    var id_len = 6+class_id.length;
    var this_id = id.substr(id_len);
    // console.log(class_id+" "+this_id);

    var data = valObj("learning_materials_holder"+class_id);
    if (hasJsonStructure(data)) {
        var lm_data = JSON.parse(data);
        if (lm_data.length > 0) {
            var new_data = [];
            for (let index = 0; index < lm_data.length; index++) {
                const element = lm_data[index];
                if (index == this_id) {
                    continue;
                }
    
                // store that data
                new_data.push(element);
            }
            cObj("learning_materials_holder"+class_id).value = JSON.stringify(new_data);
        }else{
            cObj("learning_materials_holder"+class_id).value = "";
        }
    }else{
        cObj("learning_materials_holder"+class_id).value = "";
    }
    displayLMsub(class_id);
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

var add_objective = document.getElementsByClassName("add_objective");
for (let index = 0; index < add_objective.length; index++) {
    const element = add_objective[index];
    element.addEventListener("click",addObjectives);
}

function addObjectives() {
    var id = this.id.substr(13);
    
    var err =  checkBlank("strands_objectives"+id);
    if (err == 0) {
        var data = valObj("strands_objectives_holder"+id);
        if (data.length > 0) {
            if (hasJsonStructure(data)) {
                data = JSON.parse(data);
                data.push(valObj("strands_objectives"+id));
                cObj("strands_objectives_holder"+id).value = JSON.stringify(data);
            }else{
                data = [];
                data.push(valObj("strands_objectives"+id));
                cObj("strands_objectives_holder"+id).value = JSON.stringify(data);
            }
        }else{
            data = [];
            data.push(valObj("strands_objectives"+id));
            cObj("strands_objectives_holder"+id).value = JSON.stringify(data);
        }
    }
    cObj("strands_objectives"+id).value = "";
    displayStrandObjectivesSubstrands(id);
}

function displayStrandObjectivesSubstrands(id) {
    var data = valObj("strands_objectives_holder"+id);
    if (hasJsonStructure(data)) {
        data = JSON.parse(data);
        if (data.length > 0) {
            var data_to_display = "";
            for (let index = 0; index < data.length; index++) {
                const element = data[index];
                data_to_display+="<li class='list-group-item'>"+element+"  <span style='cursor:pointer;' class='text-danger trash_objectives_"+id+"' id='t_obj_"+id+"_"+index+"'><i class='bi bi-trash'></i></span></li>";
            }
            cObj("strands_obj_list"+id).innerHTML = data_to_display;
        }else{
            cObj("strands_obj_list"+id).innerHTML = "<li class='list-group-item text-black'>No Objectives available at the moment.</li>";
        }
    }else{
        cObj("strands_obj_list"+id).innerHTML = "<li class='list-group-item text-black'>No Objectives available at the moment.</li>";
    }
    var trash_objectives = document.getElementsByClassName("trash_objectives_"+id);
    for (let index = 0; index < trash_objectives.length; index++) {
        const element = trash_objectives[index];
        element.addEventListener("click",deleteLearningObjectives);
    }
}
function deleteLearningObjectives() {
    var id = this.id;
    var class_id = this.className.split(" ")[1].substr(17);
    var id_len = 7+class_id.length;
    var this_id = id.substr(id_len);
    // console.log(class_id+" "+this_id+" "+id+" "+id_len);

    var data = valObj("strands_objectives_holder"+class_id);
    if (hasJsonStructure(data)) {
        var lm_data = JSON.parse(data);
        if (lm_data.length > 0) {
            var new_data = [];
            for (let index = 0; index < lm_data.length; index++) {
                const element = lm_data[index];
                if (index == this_id) {
                    continue;
                }
    
                // store that data
                new_data.push(element);
            }
            cObj("strands_objectives_holder"+class_id).value = JSON.stringify(new_data);
        }else{
            cObj("strands_objectives_holder"+class_id).value = "";
        }
    }else{
        cObj("strands_objectives_holder"+class_id).value = "";
    }
    displayStrandObjectivesSubstrands(class_id);
}

window.onload = function () {

    var edit_add_objective_window = document.getElementsByClassName("edit_add_objective_window");
    for (let index = 0; index < edit_add_objective_window.length; index++) {
        const element = edit_add_objective_window[index];
        element.addEventListener("click",displayEditObjective);
    }
    
    var edit_add_objective = document.getElementsByClassName("edit_add_objective");
    for (let index = 0; index < edit_add_objective.length; index++) {
        const element = edit_add_objective[index];
        element.addEventListener("click",saveEditObjectives);
    }

    var edit_learning_materials = document.getElementsByClassName("edit_learning_materials");
    for (let index = 0; index < edit_learning_materials.length; index++) {
        const element = edit_learning_materials[index];
        element.addEventListener("click",showLMList);
    }

    var  edit_learning_materials_list = document.getElementsByClassName("edit_learning_materials_list");
    for (let index = 0; index < edit_learning_materials_list.length; index++) {
        const element = edit_learning_materials_list[index];
        element.addEventListener("click",addLMLIsts);
    }

    var trash_objectives = document.getElementsByClassName("trash_objectives");
    for (let index = 0; index < trash_objectives.length; index++) {
        const element = trash_objectives[index];
        element.addEventListener("click",deleteEditObj);
    }

    var trash_learning_materials_edit = document.getElementsByClassName("trash_learning_materials_edit");
    for (let index = 0; index < trash_learning_materials_edit.length; index++) {
        const element = trash_learning_materials_edit[index];
        element.addEventListener("click",deleteLMEdited);
    }

    var add_object_windows = document.getElementsByClassName("add_object_windows");
    for (let index = 0; index < add_object_windows.length; index++) {
        const element = add_object_windows[index];
        element.addEventListener("click",showObjWindow);
    }

    var btn_add_obj = document.getElementsByClassName("btn_add_obj");
    for (let index = 0; index < btn_add_obj.length; index++) {
        const element = btn_add_obj[index];
        element.addEventListener("click",addObjectivesList);
    }
    var lm_lists = document.getElementsByClassName("lm_lists");
    for (let index = 0; index < lm_lists.length; index++) {
        const element = lm_lists[index];
        element.addEventListener("click",showLMSubStr);
    }

    var btn_add_lm_list = document.getElementsByClassName("btn_add_lm_list");
    for (let index = 0; index < btn_add_lm_list.length; index++) {
        const element = btn_add_lm_list[index];
        element.addEventListener("click",addLmListObj);
    }
    var trash_obj_del = document.getElementsByClassName("trash_obj_del");
    for (let index = 0; index < trash_obj_del.length; index++) {
        const element = trash_obj_del[index];
        element.addEventListener("click",deleteEditObj);
    }
    var getTrashObjectives = document.getElementsByClassName("getTrashObjectives");
    for (let index = 0; index < getTrashObjectives.length; index++) {
        const element = getTrashObjectives[index];
        element.addEventListener("click",deleteObj);
    }
    var trash_lm_list = document.getElementsByClassName("trash_lm_list");
    for (let index = 0; index < trash_lm_list.length; index++) {
        const element = trash_lm_list[index];
        element.addEventListener("click",trashLMsubstr);
    }
}

function addLmListObj() {
    var str_index = this.className.split(" ")[4].substr(16);
    var sub_str_index = this.id.substr(17+str_index.length);

    var err = checkBlank("learning_material_"+str_index+"_"+sub_str_index);
    if (err == 0) {
        var lm_obj = valObj("learning_material_"+str_index+"_"+sub_str_index);
        var lm_obj_list = valObj("learning_materials_holder_"+str_index+"_"+sub_str_index);
        if (hasJsonStructure(lm_obj_list)) {
            lm_obj_list = JSON.parse(lm_obj_list);
            lm_obj_list.push(lm_obj);
            cObj("learning_materials_holder_"+str_index+"_"+sub_str_index).value = JSON.stringify(lm_obj_list);
        }else{
            var obj_list = [];
            obj_list.push(lm_obj);

            cObj("learning_materials_holder_"+str_index+"_"+sub_str_index).value = JSON.stringify(obj_list);
        }
        cObj("learning_material_"+str_index+"_"+sub_str_index).value = "";
    }
    displayLMsubList(str_index,sub_str_index);
}

function displayLMsubList(str_index,sub_str_index) {
    var lm_obj = valObj("learning_materials_holder_"+str_index+"_"+sub_str_index);
    if (lm_obj.length > 0) {
        if (hasJsonStructure(lm_obj)) {
            lm_obj = JSON.parse(lm_obj);
            console.log(lm_obj);
            var data_to_display = "";
            for (let index = 0; index < lm_obj.length; index++) {
                const element = lm_obj[index];
                data_to_display+="<li class='list-group-item text-black'>"+element+" <span style='cursor:pointer;' class='text-danger trash_lm_lst_"+str_index+"_"+sub_str_index+"' id = 'lmlist_"+str_index+"_"+sub_str_index+"_"+index+"'><i class='bi bi-trash'></i></span></li>";
            }
            cObj("learning_materials_lists_"+str_index+"_"+sub_str_index).innerHTML = data_to_display;
        }else{
            cObj("learning_materials_lists_"+str_index+"_"+sub_str_index).innerHTML = "<li class='list-group-item text-black'>No learning materials available at the moment.</li>";
        }
    }else{
        cObj("learning_materials_lists_"+str_index+"_"+sub_str_index).innerHTML = "<li class='list-group-item text-black'>No learning materials available at the moment.</li>";
    }
    var events_listener = document.getElementsByClassName("trash_lm_lst_"+str_index+"_"+sub_str_index+"");
    for (let index = 0; index < events_listener.length; index++) {
        const element = events_listener[index];
        element.addEventListener("click",trashLMsubstr);
    }
}

function trashLMsubstr() {
    var indexes = this.className.split(" ")[1].substr(13).split("_");
    var str_index = indexes[0]+"_"+indexes[1];
    var sub_str_index = indexes[2];
    // get the index to be removed
    var rm_index = this.id.substr(9+str_index.length+sub_str_index.length);

    var data_ouput = valObj("learning_materials_holder_"+str_index+"_"+sub_str_index);
    if (data_ouput.length > 0) {
        if (hasJsonStructure(data_ouput)) {
            data_ouput = JSON.parse(data_ouput);
            var new_data = [];
            for (let index = 0; index < data_ouput.length; index++) {
                const element = data_ouput[index];
                if (index == rm_index) {
                    continue;
                }
                new_data.push(element);
            }
            if (new_data.length > 0) {
                cObj("learning_materials_holder_"+str_index+"_"+sub_str_index).value = JSON.stringify(new_data);
            }else{
                cObj("learning_materials_holder_"+str_index+"_"+sub_str_index).value = "";
            }
        }
    }
    displayLMsubList(str_index,sub_str_index);
}

function showLMSubStr() {
    var str_index = this.className.split(" ")[3].substr(22);
    var sub_str_index = this.id.substr(23+str_index.length);
    cObj("add_learning_materials_window_"+str_index+"_"+sub_str_index).classList.toggle("hide");
}

function addObjectivesList() {
    var str_index = this.className.split(" ")[4].substr(11);
    var sub_str_index = this.id.substr(str_index.length + 13);
    // console.log(str_index+" 2 "+sub_str_index+" 3 "+this.className);

    // get the error of the blank student objectives
    var err = checkBlank("strands_objectives_"+str_index+"_"+sub_str_index);
    if (err == 0) {
        var stored_objectives = valObj("strands_objectives_holder_"+str_index+"_"+sub_str_index);
        if (hasJsonStructure(stored_objectives)) {
            stored_objectives = JSON.parse(stored_objectives);
            if (stored_objectives.length > 0) {
                stored_objectives.push(valObj("strands_objectives_"+str_index+"_"+sub_str_index));
                
                // store objectives in the holder
                cObj("strands_objectives_holder_"+str_index+"_"+sub_str_index).value = JSON.stringify(stored_objectives);
            }else{
                var store_obj = [];
                store_obj.push(valObj("strands_objectives_"+str_index+"_"+sub_str_index));

                // store the objectives in the holder
                cObj("strands_objectives_holder_"+str_index+"_"+sub_str_index).value = JSON.stringify(store_obj);
            }
        }else{
            var store_obj = [];
            store_obj.push(valObj("strands_objectives_"+str_index+"_"+sub_str_index));

            // store the objectives in the holder
            cObj("strands_objectives_holder_"+str_index+"_"+sub_str_index).value = JSON.stringify(store_obj);
        }
        cObj("strands_objectives_"+str_index+"_"+sub_str_index).value = "";
    }

    displayStoredObjectives(str_index,sub_str_index);
}

function displayStoredObjectives(str_index,sub_str_index) {
    var stored_objectives = valObj("strands_objectives_holder_"+str_index+"_"+sub_str_index);
    if (hasJsonStructure(stored_objectives)) {
        stored_objectives = JSON.parse(stored_objectives);
        if (stored_objectives.length > 0) {
            var data_to_display = "";
            for (let index = 0; index < stored_objectives.length; index++) {
                const element = stored_objectives[index];
                data_to_display+="<li class='list-group-item text-black'>"+element+". <span style='cursor:pointer;' class='text-danger trash_obj"+str_index+"_"+sub_str_index+"' id='trash_obj_"+str_index+"_"+sub_str_index+"_"+index+"'><i class='bi bi-trash'></i></span></li>";
            }
            cObj("strands_obj_list_"+str_index+"_"+sub_str_index).innerHTML = data_to_display;
        }else{
            cObj("strands_obj_list_"+str_index+"_"+sub_str_index).innerHTML = "<li class='list-group-item text-black'>No lists Objectives at the moment.</li>";
        }
    }else{
        cObj("strands_obj_list_"+str_index+"_"+sub_str_index).innerHTML = "<li class='list-group-item text-black'>No lists Objectives at the moment.</li>";
    }
    
    var trash_objectives = document.getElementsByClassName("trash_obj"+str_index+"_"+sub_str_index+"");
    for (let index = 0; index < trash_objectives.length; index++) {
        const element = trash_objectives[index];
        element.addEventListener("click",deleteObj);
    }
}

function deleteObj() {
    var post_fix = this.className.split(" ")[1].substr(9).split("_");
    var str_index = post_fix[0]+"_"+post_fix[1];
    var sub_str_index = post_fix[2];
    var obj_index = this.id.substr(12+str_index.length+sub_str_index.length);

    console.log("strands_objectives_holder_"+str_index+"_"+sub_str_index);
    // get the value for the stored objectives
    var stored_objectives = valObj("strands_objectives_holder_"+str_index+"_"+sub_str_index);
    if (hasJsonStructure(stored_objectives)) {
        stored_objectives = JSON.parse(stored_objectives);
        var new_obj_list = [];
        for (let index = 0; index < stored_objectives.length; index++) {
            const element = stored_objectives[index];
            if (obj_index == index) {
                continue;
            }
            new_obj_list.push(element);
        }
        if (new_obj_list.length > 0) {
            cObj("strands_objectives_holder_"+str_index+"_"+sub_str_index).value = JSON.stringify(new_obj_list);
        }else{
            cObj("strands_objectives_holder_"+str_index+"_"+sub_str_index).value = "";
        }
    }else{
        cObj("strands_objectives_holder_"+str_index+"_"+sub_str_index).value = "";
    }
    displayStoredObjectives(str_index,sub_str_index);
}

function showObjWindow() {
    var str_index = this.className.split(" ")[3].substr(21);
    var sub_str_index = this.id.substr(str_index.length + 22);

    cObj("objective_record_window_"+str_index+"_"+sub_str_index).classList.toggle("hide");
}

function addLMLIsts() {
    var this_id = this.id.substr(29);
    var err = checkBlank("learning_materials_"+this_id);
    if (err == 0) {
        var learning_materials = valObj("learning_materials_"+this_id);
        var learning_materials_holder = valObj("edit_learning_materials_holder_"+this_id);
        if (learning_materials_holder.length > 0) {
            if (hasJsonStructure(learning_materials_holder)) {
                learning_materials_holder = JSON.parse(learning_materials_holder);
                learning_materials_holder.push(learning_materials);

                cObj("edit_learning_materials_holder_"+this_id).value = JSON.stringify(learning_materials_holder);
            }else{
                var lm_list = [];
                lm_list.push(learning_materials);
                
                cObj("edit_learning_materials_holder_"+this_id).value = JSON.stringify(lm_list);
            }
        }else{
            var lm_list = [];
            lm_list.push(learning_materials);
            
            cObj("edit_learning_materials_holder_"+this_id).value = JSON.stringify(lm_list);
        }
        cObj("learning_materials_"+this_id).value = "";
    }
    displayEditedLMList(this_id);
}

function displayEditedLMList(this_id) {
    var edit_learning_materials_holder = valObj("edit_learning_materials_holder_"+this_id);

    if (edit_learning_materials_holder.length > 0) {
        if (hasJsonStructure(edit_learning_materials_holder)) {
            edit_learning_materials_holder = JSON.parse(edit_learning_materials_holder);
            if (edit_learning_materials_holder.length > 0) {
                var data_to_display = "";
                for (let index = 0; index < edit_learning_materials_holder.length; index++) {
                    const element = edit_learning_materials_holder[index];
                    data_to_display+="<li class='list-group-item'>"+element+"  <span style='cursor:pointer;' class='text-danger trash_learning_materials"+this_id+"' id='trash_lm_"+this_id+"_"+index+"'><i class='bi bi-trash'></i></span></li>";
                }
                cObj("edit_learning_materials_lists_"+this_id).innerHTML = data_to_display;
            }else{
                cObj("edit_learning_materials_lists_"+this_id).innerHTML = "<li class='list-group-item text-black'>No Learning materials available at the moment.</li>";
            }
        }else{
            cObj("edit_learning_materials_lists_"+this_id).innerHTML = "<li class='list-group-item text-black'>No Learning materials available at the moment.</li>";
        }
    }else{
        cObj("edit_learning_materials_lists_"+this_id).innerHTML = "<li class='list-group-item text-black'>No Learning materials available at the moment.</li>";
    }
    
    delLMedit(this_id);
}

function delLMedit(this_id) {
    var trash_learning_materials = document.getElementsByClassName("trash_learning_materials"+this_id);
    for (let index = 0; index < trash_learning_materials.length; index++) {
        const element = trash_learning_materials[index];
        element.addEventListener("click",deleteLMEdited);
    }
}

function deleteLMEdited() {
    var ids = this.className.split(" ")[1].substr(24);
    var lm_index = this.id.substr(10+ids.length);
    console.log(this.className.split(" ")[1]);

    // delete the unwanted learning materials
    var edit_learning_materials_holder = valObj("edit_learning_materials_holder_"+ids);
    if (edit_learning_materials_holder.length > 0) {
        if (hasJsonStructure(edit_learning_materials_holder)) {
            edit_learning_materials_holder = JSON.parse(edit_learning_materials_holder);
            var new_lm_list = [];
            for (let index = 0; index < edit_learning_materials_holder.length; index++) {
                const element = edit_learning_materials_holder[index];
                if (lm_index == index) {
                    continue;
                }
                new_lm_list.push(element);
            }
            if (new_lm_list.length > 0) {
                cObj("edit_learning_materials_holder_"+ids).value = JSON.stringify(new_lm_list);
            }else{
                cObj("edit_learning_materials_holder_"+ids).value = "";
            }
        }
    }
    displayEditedLMList(ids);
}

function showLMList() {
    var this_id = this.id.substr(24);
    cObj("edit_learning_materials_window_"+this_id).classList.toggle("hide");
}

function saveEditObjectives() {
    var this_id = this.id.substr(18);
    // get the data from the input text
    var err = checkBlank("edit_strands_objectives"+this_id);
    if (err == 0) {
        var strands_objectives = valObj("edit_strands_objectives"+this_id);
        
        var objectives = valObj("edit_strands_objectives_holder"+this_id);
        if (objectives.length > 0) {
            if (hasJsonStructure(objectives)) {
                objectives = JSON.parse(objectives);
                objectives.push(strands_objectives);

                cObj("edit_strands_objectives_holder"+this_id).value = JSON.stringify(objectives);
            }else{
                var obj = [];
                obj.push(strands_objectives);

                cObj("edit_strands_objectives_holder"+this_id).value = JSON.stringify(obj);
            }
        }else{
            var obj = [];
            obj.push(strands_objectives);

            cObj("edit_strands_objectives_holder"+this_id).value = JSON.stringify(obj);
        }
    }
    cObj("edit_strands_objectives"+this_id).value = "";

    displayEditObj(this_id);
}

function displayEditObj(this_id) {
    var strands_objectives = valObj("edit_strands_objectives_holder"+this_id);
    if (strands_objectives.length > 0) {
        if (hasJsonStructure(strands_objectives)) {
            strands_objectives = JSON.parse(strands_objectives);
            if (strands_objectives.length > 0) {
                var data_to_display = "";
                for (let index = 0; index < strands_objectives.length; index++) {
                    const element = strands_objectives[index];
                    data_to_display+="<li class='list-group-item'>"+element+"  <span style='cursor:pointer;' class='text-danger trash_edit_obj"+this_id+"' id='trash_edit_obj_"+this_id+"_"+index+"'><i class='bi bi-trash'></i></span></li>";
                }
                cObj("edit_strands_obj_list"+this_id).innerHTML = data_to_display;
            }else{
                cObj("edit_strands_obj_list"+this_id).innerHTML = "<li class='list-group-item text-black'>No objectives set at the moment!</li>";
            }
        }else{
            cObj("edit_strands_obj_list"+this_id).innerHTML = "<li class='list-group-item text-black'>No objectives set at the moment!</li>";
        }
    }else{
        cObj("edit_strands_obj_list"+this_id).innerHTML = "<li class='list-group-item text-black'>No objectives set at the moment!</li>";
    }
     addListenerDelete(this_id);
}

function addListenerDelete(this_id) {
    // add an event listener to all the trash cans added
    var trash_cans = document.getElementsByClassName("trash_edit_obj"+this_id);
    for (let index = 0; index < trash_cans.length; index++) {
        const element = trash_cans[index];
        element.addEventListener("click",deleteEditObj);
    }
}

function deleteEditObj() {
    var class_name = this.className.split(" ")[1];
    var class_ids = class_name.substr(14);
    var obj_index = this.id.substr(16+(class_ids.length));
    // console.log(class_name+" "+class_ids+" "+obj_index);

    var edit_strands_objectives_holder = valObj("edit_strands_objectives_holder"+class_ids);

    if (edit_strands_objectives_holder.length > 0) {
        if (hasJsonStructure(edit_strands_objectives_holder)) {
            edit_strands_objectives_holder = JSON.parse(edit_strands_objectives_holder);
            var new_strands_objs = [];
            for (let index = 0; index < edit_strands_objectives_holder.length; index++) {
                const element = edit_strands_objectives_holder[index];
                if (obj_index == index) {
                    continue;
                }
                new_strands_objs.push(element);
            }
            if (new_strands_objs.length > 0) {
                cObj("edit_strands_objectives_holder"+class_ids).value = JSON.stringify(new_strands_objs);
            }else{
                cObj("edit_strands_objectives_holder"+class_ids).value = "";
            }
        }
    }
    displayEditObj(class_ids);

}

function displayEditObjective() {
    var this_id = this.id.substr(25);
    cObj("edit_objective_record_window"+this_id).classList.toggle("hide");
}
var substrand_locale_opt = document.getElementsByClassName("substrand_locale_opt");
for (let index = 0; index < substrand_locale_opt.length; index++) {
    const element = substrand_locale_opt[index];
    element.addEventListener("change",showWindow);
}

function showWindow() {
    var this_id = this.id.substr(20);
    var this_value = this.value;
    if (this_value == "Different Strand") {
        cObj("different_strand_"+this_id).classList.remove("hide");
        cObj("different_loc_"+this_id).classList.add("hide");
    }else if (this_value == "In Strand") {
        cObj("different_loc_"+this_id).classList.remove("hide");
        cObj("different_strand_"+this_id).classList.add("hide");
    }
}

var delete_strand = document.getElementsByClassName("delete_strand");
for (let index = 0; index < delete_strand.length; index++) {
    const element = delete_strand[index];
    element.addEventListener("click",delete_strand_window);
}

function delete_strand_window() {
    var this_id = this.id.substr(13);
    cObj("delete_strand_window"+this_id).classList.toggle("hide");
    if (cObj("delete_strand_window"+this_id).classList.contains("hide")) {
        cObj(this.id).innerHTML = "<i class='bi bi-trash'></i> Delete";
    }else{
        cObj(this.id).innerHTML = "<i class='bi bi-x'></i> Cancel";
    }
}

var delete_sub_strand = document.getElementsByClassName("delete_sub_strand");
for (let index = 0; index < delete_sub_strand.length; index++) {
    const element = delete_sub_strand[index];
    element.addEventListener("click",delete_sub_strand_window);
}

function delete_sub_strand_window() {
    var this_id = this.id.substr(17);
    cObj("delete_sub_strand_window"+this_id).classList.toggle("hide");
    if (cObj("delete_sub_strand_window"+this_id).classList.contains("hide")) {
        cObj(this.id).innerHTML = "<i class='bi bi-trash'></i> Delete";
    }else{
        cObj(this.id).innerHTML = "<i class='bi bi-x'></i> Cancel";
    }
}
// end long term plan
