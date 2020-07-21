/****************************************************************************
 * Copyright (C) 2020, Sarah-Jane Whittaker (sarah@cs.queensu.ca)
 *
 * This program is free software: you can redistribute it and/or modify it 
 * under the terms of the GNU Affero General Public License as published by 
 * the Free Software Foundation, either version 3 of the License, or (at 
 * your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero 
 * General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License 
 * along with this program in the file gnu-agpl-3.0.txt. If not, 
 * see <https://www.gnu.org/licenses/>.
 * 
 * This program was developed with the input and support of the Queen's
 * University School of Computing <https://www.cs.queensu.ca/>, Faculty
 * of Arts and Science <https://www.queensu.ca/artsci/> and the Office of 
 * the Vice-Provost (Teaching and Learning) 
 * <https://www.queensu.ca/provost/teaching-and-learning>.
 ***************************************************************************/


/******************************************************************************
 * Constants
 *****************************************************************************/
const QSC_CMP_FORM_PLLO_ADD = "#form-pllo-add";
const QSC_CMP_FORM_PLLO_EDIT = "#form-pllo-edit";
const QSC_CMP_FORM_PLLO_DELETE = "#form-pllo-delete";

const QSC_CMP_FORM_PLLO_ID = "#pllo-id";
const QSC_CMP_FORM_PLLO_NUMBER = "#pllo-number";

const QSC_CMP_FORM_PLLO_PLAN_INPUT = "#pllo-plan-input";
const QSC_CMP_FORM_PLLO_PLAN_SELECT = "#pllo-plan-select";

const QSC_CMP_FORM_PLLO_PARENT_DLE_SELECT = "#pllo-parent-dle-select";
const QSC_CMP_FORM_PLLO_PARENT_DLE_UNSELECT = "#pllo-parent-dle-unselect";

const QSC_CMP_FORM_PLLO_PARENT_PLLO_SELECT = "#pllo-parent-pllo-select";
const QSC_CMP_FORM_PLLO_PARENT_PLLO_UNSELECT = "#pllo-parent-pllo-unselect";

const QSC_CMP_FORM_PLLO_TEXT = "#pllo-text";

const QSC_CMP_FORM_PLLO_ILO_LIST_POSSIBLE = "#pllo-ilo-list-possible";
const QSC_CMP_FORM_PLLO_ILO_LIST_SUPPORTED = "#pllo-ilo-list-supported";
const QSC_CMP_FORM_PLLO_ILO_ADD = "#pllo-ilo-add";
const QSC_CMP_FORM_PLLO_ILO_REMOVE = "#pllo-ilo-remove"; 

const QSC_CMP_FORM_PLLO_NOTES = "#pllo-notes";


/******************************************************************************
 * Functions
 *****************************************************************************/
function qscCMPPLLOHandlePlanInput() {
    // Get the current value that the user's entered in the input box
    let currentPlanValue = $(this).val();

    // Prep the AJAX data
    let ajaxData = {action: QSC_CMP_AJAX_ACTION_SEARCH_PLANS,
        search: currentPlanValue
    };
    
    // Get the select box that goes with the input box and remove all
    // the current options
    let planSelect = $(QSC_CMP_FORM_PLLO_PLAN_SELECT);
    planSelect.find("option").remove();

    qscCorePerformAJAXRequest(QSC_CMP_AJAX_SCRIPT_GET_PLANS, ajaxData,
        function (jsonData) {
            // Create new options from the JSON data and put them in the select
            planSelect.append(qscCoreCreateOptionsFromJSONData(jsonData, 'id', 'name'));

            // Did the update eliminate the previous selection? If so, remove
            // all options from the PLLO list except "None".
            let selectedPlan = planSelect.find("option:selected");
            if (!selectedPlan.length) {
                $(QSC_CMP_FORM_PLLO_PARENT_PLLO_SELECT).find("option").remove();
            }
        }
    );    
}

function qscCMPPLLOHandlePlanSelection() {
    // Get the current PLLO ID and the plan selection box
    let currentPLLOID = $(QSC_CMP_FORM_PLLO_ID).val();
    let planSelect = $(QSC_CMP_FORM_PLLO_PLAN_SELECT);

    // Any change to the plan selection means removing the prior
    // parent PLLO options
    let parentPLLOSelect = $(QSC_CMP_FORM_PLLO_PARENT_PLLO_SELECT);
    parentPLLOSelect.find("option").remove();

    // Get the selected option from the list of plans and check that
    // the change was to select, not unselect
    let selectedPlan = planSelect.find("option:selected");
    if (! selectedPlan.length) {
        return;
    }

    // Prep the AJAX data
    let ajaxData = {action: QSC_CMP_AJAX_ACTION_GET_PLLOS_FOR_PLAN,
        id: selectedPlan.val()
    };
    
    qscCorePerformAJAXRequest(QSC_CMP_AJAX_SCRIPT_GET_PLLOS, ajaxData,
        function (jsonData) {
            // Create new options from the JSON data and put them in the select
            parentPLLOSelect.append(
                qscCoreCreateOptionsFromJSONData(jsonData, 'id', 'name', currentPLLOID));
        }
    );
}


/******************************************************************************
 * Document - Ready
 *****************************************************************************/
$(document).ready(function() {
    // Handle the user editing the selected course
    $(QSC_CMP_FORM_PLLO_PLAN_INPUT).keyup(qscCMPPLLOHandlePlanInput);
    
    // Handle the user changing the selected course, which must change
    // the possible parent PLLO options
    $(QSC_CMP_FORM_PLLO_PLAN_SELECT).change(qscCMPPLLOHandlePlanSelection);     
    
    // Handle the user 'unselecting' the DLE because Ctrl + click
    // doesn't unselect an option in a single select box
    qscCoreUnselectWithButton(QSC_CMP_FORM_PLLO_PARENT_DLE_UNSELECT, 
        QSC_CMP_FORM_PLLO_PARENT_DLE_SELECT);
    qscCoreUnselectWithButton(QSC_CMP_FORM_PLLO_PARENT_PLLO_UNSELECT, 
        QSC_CMP_FORM_PLLO_PARENT_PLLO_SELECT);
            
    // If a parent PLLO is selected, any selected DLE should be unselected
    // and vice-versa.
    qscCoreUnselectOnChange(QSC_CMP_FORM_PLLO_PARENT_DLE_SELECT,
        QSC_CMP_FORM_PLLO_PARENT_PLLO_SELECT);     
    qscCoreUnselectOnChange(QSC_CMP_FORM_PLLO_PARENT_PLLO_SELECT,
        QSC_CMP_FORM_PLLO_PARENT_DLE_SELECT);
        
    // Handle the user selecting the '>>' and '<<' buttons to move PLLOs
    // and ILOs back and forth
    qscCoreTransferOptionOnClick(QSC_CMP_FORM_PLLO_ILO_ADD,
        QSC_CMP_FORM_PLLO_ILO_LIST_POSSIBLE, 
        QSC_CMP_FORM_PLLO_ILO_LIST_SUPPORTED);
    qscCoreTransferOptionOnClick(QSC_CMP_FORM_PLLO_ILO_REMOVE,
        QSC_CMP_FORM_PLLO_ILO_LIST_SUPPORTED, 
        QSC_CMP_FORM_PLLO_ILO_LIST_POSSIBLE);        
    
    // Handle form submission for add and edit
    $(QSC_CMP_FORM_PLLO_ADD + ", " + QSC_CMP_FORM_PLLO_EDIT).submit(function(event) {            
        // Either a DLE or parent PLLO must be selected
        let dleSelected = $(QSC_CMP_FORM_PLLO_PARENT_DLE_SELECT + ' option:selected').length;
        let parentPLLOSelected = $(QSC_CMP_FORM_PLLO_PARENT_PLLO_SELECT + ' option:selected').length;
        
        if ((dleSelected + parentPLLOSelected) === 0) {
            // To Do: figure out an accessible way to communicate the problem
            event.preventDefault();
            return;
        }
        
        // Select all of the options in the supported ILOs list so they 
        // // appear in $_POST
        qscCoreSelectAllOptions(QSC_CMP_FORM_PLLO_ILO_LIST_SUPPORTED);
                
        // Set the flag that JS is working
        qscCMPSetJSCompletedOnSubmission(); 
    });
    
    // Handle form submission for delete
    $(QSC_CMP_FORM_PLLO_DELETE).submit(function() {
        return qscCoreConfirmDelete("PLLO");
    });     
    
});