/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

Date.prototype.monthDays = function() {
    var d = new Date(this.getFullYear(), this.getMonth() + 1, 0);
    return d.getDate();
}

function render_roomavailability_table_header(selectedDate) {
    var dayNames = new Array("S","M","D","M","D","F","S");
    var days = selectedDate.monthDays();
    var month = selectedDate.getMonth();
    var year = selectedDate.getYear();
    var header = jQuery("<thead/>");
    var headerRow = jQuery("<tr/>");
    jQuery("<th/>").appendTo(headerRow);
    for (i = 1; i <= days; i++) {
        var date = new Date(1900 + year, month + 1, i);
        jQuery("<th/>", {
            html: dayNames[date.getDay()]
        }).appendTo(headerRow);
    }
    headerRow.appendTo(header);
    headerRow = jQuery("<tr/>");
    jQuery("<th/>").appendTo(headerRow);
    for (i = 1; i <= days; i++) {
        jQuery("<th/>", {
            html: i
        }).appendTo(headerRow);
    }

    headerRow.appendTo(header);
    return header;
}

function render_roomavailability_table_body(selectedDate) {
    var days = selectedDate.monthDays();
    var body = jQuery("<tbody/>");
    for (i = 0; i < availabilities.length; i++) {
        var room = availabilities[i];
        var bodyRow = jQuery("<tr/>");

        jQuery("<td/>", {
            html: room["room_name"],
            "class": "caption"
        }).appendTo(bodyRow);

        for (j = 1; j <= days; j++) {
            var cellContent = "&nbsp;";
            var availableDates = room["availability"];
            var day = j < 10 ? "0" + j : j;
            var month = selectedDate.getMonth() + 1 < 10 ? "0" + (selectedDate.getMonth() + 1) : selectedDate.getMonth() + 1;
            var year = selectedDate.getYear() + 1900;
            var date = day + "." + month + "." + year;
            var styleClass = "";
            if (jQuery.inArray(date, availableDates) !== -1) {
                styleClass = "available";
            }
            jQuery("<td/>", {
                "class": styleClass
            }).appendTo(bodyRow);
        }

        bodyRow.appendTo(body);
    }
    return body;
}

function render_roomavailability() {
    render_roomavailability_table();

    jQuery("#month").change(function() {render_roomavailability_table()});
    jQuery("#year").change(function() {render_roomavailability_table()});
}

function render_roomavailability_table() {
    jQuery("#roomavailability table").remove();

    var selectedMonth = jQuery("#month option:selected").attr("value");
    var selectedYear = jQuery("#year option:selected").attr("value");

    var selectedDate = new Date();
    selectedDate.setDate(1);
    selectedDate.setFullYear(selectedYear);
    selectedDate.setMonth(selectedMonth - 1);

    var table = jQuery("<table/>");

    render_roomavailability_table_header(selectedDate).appendTo(table);
    render_roomavailability_table_body(selectedDate).appendTo(table);

    table.appendTo("#roomavailability");
}
