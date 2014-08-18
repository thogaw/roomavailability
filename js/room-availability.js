/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function render_roomavailability_table() {
    var table = jQuery("<table/>");

    // Build header
    var header = jQuery("<thead/>");
    var headerRow = jQuery("<tr/>");
    jQuery("<th/>").appendTo(headerRow);
    for (i = 1; i <= 31; i++) {
        jQuery("<th/>", {
            html: i
        }).appendTo(headerRow);
    }

    headerRow.appendTo(header);
    header.appendTo(table);

    // Build body
    var body = jQuery("<tbody/>");
    for (i = 0; i < availabilities.length; i++) {
        var room = availabilities[i];
        var bodyRow = jQuery("<tr/>");

        jQuery("<td/>", {
            html: room["room_name"],
            "class": "caption"
        }).appendTo(bodyRow);

        for (j = 1; j <= 31; j++) {
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
    body.appendTo(table);

    table.appendTo("#roomavailability");
}
