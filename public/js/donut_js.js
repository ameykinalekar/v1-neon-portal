const segments = $(".donut-segment");

// First segment's offset
const firstSegmentOffset = parseInt($(segments[0]).attr("stroke-dashoffset"));

// Total of all preceding segments length
// For use in stroke-dashoffset calculation
let preSegmentsTotalLength = $(segments[0]).data("per");

for (let i = 0; i < segments.length; i++) {
    // percentage occupied by current segment
    let percent = $(segments[i]).data("per");

    // current segments stroke-dasharray calculation
    let strokeDasharray = `${percent} ${100 - percent}`;

    // setting stroke-dasharray for all segments
    $(segments[i]).css("stroke-dasharray", strokeDasharray);

    if (i != 0) {
        // current segments stroke-dashoffset calculation
        let strokeDashoffset = `${100 - preSegmentsTotalLength + firstSegmentOffset}`;

        // setting stroke-dasharray for all segments
        $(segments[i]).css("stroke-dashoffset", strokeDashoffset);

        // Updating total of all preceding segments length
        preSegmentsTotalLength += percent;
    }
}