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
const QSC_CMP_FORM_SEARCH = "#search-form";
const QSC_CMP_FORM_SEARCH_TEXT = "#search-text";

const QSC_CMP_FORM_SEARCH_OPTION_CLLOS = "#search-option-cllos";
const QSC_CMP_FORM_SEARCH_OPTION_COURSES = "#search-option-courses";
const QSC_CMP_FORM_SEARCH_OPTION_DEGREES = "#search-option-degrees";
const QSC_CMP_FORM_SEARCH_OPTION_DLES = "#search-option-dles";
const QSC_CMP_FORM_SEARCH_OPTION_DEPARTMENTS = "#search-option-departments";
const QSC_CMP_FORM_SEARCH_OPTION_FACULTIES = "#search-option-faculties";
const QSC_CMP_FORM_SEARCH_OPTION_ILOS = "#search-option-ilos";
const QSC_CMP_FORM_SEARCH_OPTION_PLANS = "#search-option-plans";
const QSC_CMP_FORM_SEARCH_OPTION_PLLOS = "#search-option-pllos";
const QSC_CMP_FORM_SEARCH_OPTION_PROGRAMS = "#search-option-programs";
const QSC_CMP_FORM_SEARCH_OPTION_REVISIONS = "#search-option-revisions";

const QSC_CMP_FORM_SEARCH_RESULTS_CLLOS = "#search-results-cllos";
const QSC_CMP_FORM_SEARCH_RESULTS_COURSES = "#search-results-courses";
const QSC_CMP_FORM_SEARCH_RESULTS_DEGREES = "#search-results-degrees";
const QSC_CMP_FORM_SEARCH_RESULTS_DEPARTMENTS = "#search-results-departments";
const QSC_CMP_FORM_SEARCH_RESULTS_DLES = "#search-results-dles";
const QSC_CMP_FORM_SEARCH_RESULTS_FACULTIES = "#search-results-faculties";
const QSC_CMP_FORM_SEARCH_RESULTS_ILOS = "#search-results-ilos";
const QSC_CMP_FORM_SEARCH_RESULTS_PLANS = "#search-results-plans";
const QSC_CMP_FORM_SEARCH_RESULTS_PLLOS = "#search-results-pllos";
const QSC_CMP_FORM_SEARCH_RESULTS_PROGRAMS = "#search-results-programs";
const QSC_CMP_FORM_SEARCH_RESULTS_REVISIONS = "#search-results-revisions";

const LIST_ITEM_NO_RESULTS = "<li>No results found.</li>";
 

/******************************************************************************
 * Search Form Functions
 *****************************************************************************/
function showAndHideResults(checkboxObject, resultsID) {
    if (checkboxObject.checked) {
        $(resultsID).show(300);
    }
    else {
        $(resultsID).hide(300);
    }
}

function updateSearchResults(formOptionID, formResultsID, jsonData) {
    let searchResultsList = $(formResultsID + " ul");

    // Remove all previous values
    $(formResultsID + " ul li").remove();

    // Check whether there are any results
    if ((! jsonData) || (jsonData.length == 0)) {
        searchResultsList.append(LIST_ITEM_NO_RESULTS);
        return;
    }

    let results = "";
    // Add new list items
    for (let i = 0; i < jsonData.length; i++) {
        results += jsonData[i].name + ", ";

        let courseInfo = jsonData[i].name;
        if (jsonData[i].link) {
            courseInfo = '<a href="' + jsonData[i].link + '">' + courseInfo + '</a>';
        }

        searchResultsList.append("<li>" + courseInfo + "</li>");
    }

    let areResultsVisible = $(formOptionID).prop('checked');
    let searchResultsContainer = $(formResultsID);
    if (areResultsVisible && searchResultsContainer.is(":hidden")) {
        searchResultsContainer.show(300);
    }
}

function getSearchResults(ajaxScript, ajaxAction, searchValue, formOptionID, formResultsID) {
    let jqxhr = $.ajax({
        method: "POST",
        url: ajaxScript,
        dataType: 'json',
        data: { action : ajaxAction,
        search : searchValue }
    })
    .done(function(jsonData) {
        updateSearchResults(formOptionID, formResultsID, jsonData);
    })
    .fail(function() {
    });
}

function setNoResults(formResultsID) {
    $(formResultsID + " ul li").remove();
    $(formResultsID + " ul").append(LIST_ITEM_NO_RESULTS);    
}


/******************************************************************************
 * Attaching Event Handlers
 *****************************************************************************/
$(document).ready(function() {
    /* TO DO
    let ajaxScriptArray = [];
    let ajaxSearchArray = [];
    let optionElementArray = [];
    let resultsElementArray = [];        
     */           $(QSC_CMP_FORM_SEARCH_RESULTS_COURSES + " ul li").remove();
           $(QSC_CMP_FORM_SEARCH_RESULTS_COURSES + " ul").append(LIST_ITEM_NO_RESULTS);

    
    // Handle the user entering information in the search text box
    $(QSC_CMP_FORM_SEARCH_TEXT).keyup(function(event) {
       // Get the current value that the user's entered in the input box
       let currentValue = $(this).val();

       if (currentValue) {
            // If there's search text, perform AJAX queries to get the search
            // results and update the lists
            getSearchResults(QSC_CMP_AJAX_SCRIPT_GET_CLLOS,
                QSC_CMP_AJAX_ACTION_SEARCH_CLLOS, currentValue, 
                QSC_CMP_FORM_SEARCH_OPTION_CLLOS, QSC_CMP_FORM_SEARCH_RESULTS_CLLOS);
            getSearchResults(QSC_CMP_AJAX_SCRIPT_GET_COURSES, 
                QSC_CMP_AJAX_ACTION_SEARCH_COURSES, currentValue, 
                QSC_CMP_FORM_SEARCH_OPTION_COURSES, QSC_CMP_FORM_SEARCH_RESULTS_COURSES);
            getSearchResults(QSC_CMP_AJAX_SCRIPT_GET_DEGREES, 
                QSC_CMP_AJAX_ACTION_SEARCH_DEGREES, currentValue, 
                QSC_CMP_FORM_SEARCH_OPTION_DEGREES, QSC_CMP_FORM_SEARCH_RESULTS_DEGREES);
            getSearchResults(QSC_CMP_AJAX_SCRIPT_GET_DEPARTMENTS, 
                QSC_CMP_AJAX_ACTION_SEARCH_DEPARTMENTS, currentValue, 
                QSC_CMP_FORM_SEARCH_OPTION_DEPARTMENTS, QSC_CMP_FORM_SEARCH_RESULTS_DEPARTMENTS);
            getSearchResults(QSC_CMP_AJAX_SCRIPT_GET_DLES, 
                QSC_CMP_AJAX_ACTION_SEARCH_DLES, currentValue, 
                QSC_CMP_FORM_SEARCH_OPTION_DLES, QSC_CMP_FORM_SEARCH_RESULTS_DLES);
            getSearchResults(QSC_CMP_AJAX_SCRIPT_GET_FACULTIES, 
                QSC_CMP_AJAX_ACTION_SEARCH_FACULTIES, currentValue, 
                QSC_CMP_FORM_SEARCH_OPTION_FACULTIES, QSC_CMP_FORM_SEARCH_RESULTS_FACULTIES);
            getSearchResults(QSC_CMP_AJAX_SCRIPT_GET_ILOS, 
                QSC_CMP_AJAX_ACTION_SEARCH_ILOS, currentValue, 
                QSC_CMP_FORM_SEARCH_OPTION_ILOS, QSC_CMP_FORM_SEARCH_RESULTS_ILOS);
            getSearchResults(QSC_CMP_AJAX_SCRIPT_GET_PLANS, 
                QSC_CMP_AJAX_ACTION_SEARCH_PLANS, currentValue, 
                QSC_CMP_FORM_SEARCH_OPTION_PLANS, QSC_CMP_FORM_SEARCH_RESULTS_PLANS);
            getSearchResults(QSC_CMP_AJAX_SCRIPT_GET_PLLOS, 
                QSC_CMP_AJAX_ACTION_SEARCH_PLLOS, currentValue, 
                QSC_CMP_FORM_SEARCH_OPTION_PLLOS, QSC_CMP_FORM_SEARCH_RESULTS_PLLOS);
            /*
            getSearchResults(QSC_CMP_AJAX_SCRIPT_GET_PROGRAMS, 
                QSC_CMP_AJAX_ACTION_SEARCH_PROGRAMS, currentValue, 
                QSC_CMP_FORM_SEARCH_OPTION_PROGRAMS, QSC_CMP_FORM_SEARCH_RESULTS_PROGRAMS);
             */
            getSearchResults(QSC_CMP_AJAX_SCRIPT_GET_REVISIONS, 
                QSC_CMP_AJAX_ACTION_SEARCH_REVISIONS, currentValue, 
                QSC_CMP_FORM_SEARCH_OPTION_REVISIONS, QSC_CMP_FORM_SEARCH_RESULTS_REVISIONS);
       }
       else {
           // If there's no search text, remove all current results
           setNoResults(QSC_CMP_FORM_SEARCH_RESULTS_CLLOS);
           setNoResults(QSC_CMP_FORM_SEARCH_RESULTS_DEGREES);
           setNoResults(QSC_CMP_FORM_SEARCH_RESULTS_DLES);
           setNoResults(QSC_CMP_FORM_SEARCH_RESULTS_DEPARTMENTS);
           setNoResults(QSC_CMP_FORM_SEARCH_RESULTS_FACULTIES);
           setNoResults(QSC_CMP_FORM_SEARCH_RESULTS_ILOS);
           setNoResults(QSC_CMP_FORM_SEARCH_RESULTS_PLANS);
           setNoResults(QSC_CMP_FORM_SEARCH_RESULTS_PLLOS);
           //setNoResults(QSC_CMP_FORM_SEARCH_RESULTS_PROGRAMS);
           setNoResults(QSC_CMP_FORM_SEARCH_RESULTS_REVISIONS);
        }
    });

    // Handle the user (un)checking each of the types of results
    $(QSC_CMP_FORM_SEARCH_OPTION_CLLOS).click(function() {
        showAndHideResults(this, QSC_CMP_FORM_SEARCH_RESULTS_CLLOS);
    });
    $(QSC_CMP_FORM_SEARCH_OPTION_COURSES).click(function() {
        showAndHideResults(this, QSC_CMP_FORM_SEARCH_RESULTS_COURSES);
    });
    $(QSC_CMP_FORM_SEARCH_OPTION_DEGREES).click(function() {
        showAndHideResults(this, QSC_CMP_FORM_SEARCH_RESULTS_DEGREES);
    });
    $(QSC_CMP_FORM_SEARCH_OPTION_DEPARTMENTS).click(function() {
        showAndHideResults(this, QSC_CMP_FORM_SEARCH_RESULTS_DEPARTMENTS);
    });
    $(QSC_CMP_FORM_SEARCH_OPTION_DLES).click(function() {
        showAndHideResults(this, QSC_CMP_FORM_SEARCH_RESULTS_DLES);
    });
    $(QSC_CMP_FORM_SEARCH_OPTION_FACULTIES).click(function() {
        showAndHideResults(this, QSC_CMP_FORM_SEARCH_RESULTS_FACULTIES);
    });
    $(QSC_CMP_FORM_SEARCH_OPTION_ILOS).click(function() {
        showAndHideResults(this, QSC_CMP_FORM_SEARCH_RESULTS_ILOS);
    });
    $(QSC_CMP_FORM_SEARCH_OPTION_PLANS).click(function() {
        showAndHideResults(this, QSC_CMP_FORM_SEARCH_RESULTS_PLANS);
    });
    $(QSC_CMP_FORM_SEARCH_OPTION_PLLOS).click(function() {
        showAndHideResults(this, QSC_CMP_FORM_SEARCH_RESULTS_PLLOS);
    });
    /*
    $(QSC_CMP_FORM_SEARCH_OPTION_PROGRAMS).click(function() {
        showAndHideResults(this, QSC_CMP_FORM_SEARCH_RESULTS_PROGRAMS);
    });
     */
    $(QSC_CMP_FORM_SEARCH_OPTION_REVISIONS).click(function() {
        showAndHideResults(this, QSC_CMP_FORM_SEARCH_RESULTS_REVISIONS);
    });
});
