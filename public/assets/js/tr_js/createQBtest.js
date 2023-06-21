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

cObj("set_instructions_btn").onclick = function () {
    var err = checkBlank("set_instructions");
    if (err == 0) {
        var stored_instructions = valObj("my_instructions");
        if (hasJsonStructure(stored_instructions)) {
            stored_instructions = JSON.parse(stored_instructions);
            stored_instructions.push(valObj("set_instructions"));

            // store the data back
            cObj("my_instructions").value = JSON.stringify(stored_instructions);
        }else{
            var data = [];
            data.push(valObj("set_instructions"));
            cObj("my_instructions").value = JSON.stringify(data);
        }
        cObj("set_instructions").value = "";
        displayInstructions();
    }
}

window.onload = function () {
    displayInstructions();
}

function displayInstructions() {
    var stored_instructions = valObj("my_instructions");
    if (hasJsonStructure(stored_instructions)) {
        stored_instructions = JSON.parse(stored_instructions);

        // add the list
        var data_to_display = "";
        for (let index = 0; index < stored_instructions.length; index++) {
            const element = stored_instructions[index];
            data_to_display+="<li class='list-group-item'>"+(index+1)+". "+element+" <span style='cursor:pointer;' class='text-danger trash_instructions' id='trash_instructions_"+index+"'><i class='bi bi-trash'></i></span></li>";
        }

        cObj("display_objectives").innerHTML = data_to_display;

        // display data
        var trash_instructions = document.getElementsByClassName("trash_instructions");
        for (let index = 0; index < trash_instructions.length; index++) {
            const element = trash_instructions[index];
            element.addEventListener("click",removeInstructions);
        }
    }else{
        cObj("display_objectives").innerHTML = "<p class='text-secondary text-center'>No Instructions set!</p>";
    }
}

function removeInstructions() {
    var this_id = this.id.substring(19);

    var my_instructions = valObj("my_instructions");
    if (hasJsonStructure(my_instructions)) {
        my_instructions = JSON.parse(my_instructions);
        var new_data = [];
        for (let index = 0; index < my_instructions.length; index++) {
            const element = my_instructions[index];
            if (index == this_id) {
                continue;
            }
            new_data.push(element);
        }

        // save the data
        cObj("my_instructions").value = JSON.stringify(new_data);
    }else{
        cObj("my_instructions").value = "[]";
    }

    // get the data
    displayInstructions();
}

cObj("question_topic").onchange = function () {
    if (this.value == "Random") {
        cObj("topics_selectors").classList.add("hide");
    }else{
        cObj("topics_selectors").classList.remove("hide");
    }
}

function childrenListener() {
    var topic_id = this.className.substring(9);
    var subtopic_id = this.id.substring(this.className.length+1);
    // topic selected
    if (this.checked) {
        var topic_subtopic = valObj("topics_selected");
        if (hasJsonStructure(topic_subtopic)) {
            topic_subtopic = JSON.parse(topic_subtopic);
    
            // loop through the data to get the stored data
            var incase_absent = {
                topic:topic_id,
                subtopics:[subtopic_id]
            }
            var present = 0;
            for (let index = 0; index < topic_subtopic.length; index++) {
                const element = topic_subtopic[index];
                if (element.topic == topic_id) {
                    element.subtopics.push(subtopic_id);
                    present = 1;
                }
            }
    
            if (present == 0) {
                topic_subtopic.push(incase_absent);
            }
    
            // save it back
            cObj("topics_selected").value = JSON.stringify(topic_subtopic);
        }else{
            var incase_absent = [{
                topic:topic_id,
                subtopics:[subtopic_id]
            }]
            cObj("topics_selected").value = JSON.stringify(incase_absent);
        }
    }else{
        var topic_subtopic = valObj("topics_selected");
        if (hasJsonStructure(topic_subtopic)) {
            topic_subtopic = JSON.parse(topic_subtopic);

            for (let index = 0; index < topic_subtopic.length; index++) {
                const element = topic_subtopic[index];
                if (element.topic == topic_id) {
                    // create a new id holder
                    var new_holder = [];
                    for (let ind = 0; ind < element.subtopics.length; ind++) {
                        const elems = element.subtopics[ind];
                        if (elems == subtopic_id) {
                            continue;
                        }
                        new_holder.push(elems);
                    }
                    // add the data
                    element.subtopics = new_holder;
                }
            }
            
            // save it back
            cObj("topics_selected").value = JSON.stringify(topic_subtopic);
        }else{
            var incase_absent = [{
                topic:topic_id,
                subtopics:[]
            }]
            cObj("topics_selected").value = JSON.stringify(incase_absent);
        }
    }
}

var selected = document.getElementsByClassName("selected");
for (let index = 0; index < selected.length; index++) {
    const element = selected[index];
    element.addEventListener("change",function () {
        var this_id = element.id.substring(9);// this is the topic index
        var subchildren = document.getElementsByClassName("selected_"+this_id);
        
        // loop through the data
        var subtopics_store = [];
        for (let ind = 0; ind < subchildren.length; ind++) {
            const elem = subchildren[ind];
            elem.checked = element.checked;
            var subtopic_id = elem.id.substring(elem.className.length+1);

            // add event listeners
            elem.addEventListener("change",childrenListener);

            // store the subtopic id
            subtopics_store.push(subtopic_id);
        }

        // create the json object to store the topics and subtopics
        var topic_data = {
            topic:this_id,
            subtopics:subtopics_store
        }

        // if element is checked
        if (element.checked) {
            // get the data store and add the contents for this subtopics
            var topic_subtopic = valObj("topics_selected");
            if (hasJsonStructure(topic_subtopic)) {
                topic_subtopic = JSON.parse(topic_subtopic);

                var is_present = 0;
                for (let ind = 0; ind < topic_subtopic.length; ind++) {
                    const elems = topic_subtopic[ind];
                    if (elems.topic == this_id) {
                        elems.subtopics = subtopics_store
                        is_present = 1;
                    }
                }
                if (is_present == 0) {
                    topic_subtopic.push(topic_data);
                }
                // store the data back
                cObj("topics_selected").value = JSON.stringify(topic_subtopic);
            }else{
                cObj("topics_selected").value = "[]";
            }
        }else{
            // get the data store and remove the contents for this subtopics
            var topic_subtopic = valObj("topics_selected");
            if (hasJsonStructure(topic_subtopic)) {
                topic_subtopic = JSON.parse(topic_subtopic);
                var new_topic_subtopic = [];
                
                for (let ind = 0; ind < topic_subtopic.length; ind++) {
                    const elems = topic_subtopic[ind];
                    if (elems.topic == this_id) {
                        continue;
                    }
                    new_topic_subtopic.push(elems);
                }
                // store the data back
                cObj("topics_selected").value = JSON.stringify(new_topic_subtopic);
            }else{
                cObj("topics_selected").value = "[]";
            }
        }
    });
}