/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

Date.prototype.monthDays = function() {
    var d = new Date(this.getFullYear(), this.getMonth() + 1, 0);
    return d.getDate();
}

function render_roomavailability_table_header(days) {
    var header = jQuery("<thead/>");
    var headerRow = jQuery("<tr/>");
    jQuery("<th/>").appendTo(headerRow);
    for (i = 1; i <= days; i++) {
        jQuery("<th/>", {
            html: i
        }).appendTo(headerRow);
    }

    headerRow.appendTo(header);
    return header;
}

function render_roomavailability_table_body(days) {
    var body = jQuery("<tbody/>");
    for (i = 0; i < availabilities.length; i++) {
        var room = availabilities[i];
        var bodyRow = jQuery("<tr/>");

        jQuery("<td/>", {
            html: room["room_name"],
            "class": "caption"
        }).appendTo(bodyRow);

        for (j = 1; j <= days; j++) {
            var cellContent = "B";
            var availableDates = room["availability"];
            var day = j < 10 ? "0" + j : j;
            var date = day + ".08.2014";
            var styleClass = "";
            if (jQuery.inArray(date, availableDates) !== -1) {
                cellContent = "A";
                styleClass = "available";
            }
            jQuery("<td/>", {
                html: cellContent,
                "class": styleClass
            }).appendTo(bodyRow);
        }

        bodyRow.appendTo(body);
    }
    return body;
}

function render_roomavailability_table() {
    var table = jQuery("<table/>");
    var months = new Array("Januar", "Februar", "M&auml;rz", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember");
    var currentDate = new Date();

    var currentMonthDays = currentDate.monthDays();

    render_roomavailability_table_header(currentMonthDays).appendTo(table);
    render_roomavailability_table_body(currentMonthDays).appendTo(table);

    table.appendTo("#roomavailability");
}
