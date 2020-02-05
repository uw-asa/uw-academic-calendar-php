<?php

/*

UWAcademicCalendar - Calculate the dates of the University of Washington academic calendar for any given year.

Follows the Washington state law defining the university calendar:

http://apps.leg.wa.gov/wac/default.aspx?cite=478-132-030

WAC 478-132-030

University calendar.

The calendar at the university consists of four quarters, which normally begin and end as follows:

 (1) The autumn quarter shall begin on the last Wednesday in September and end on the twelfth Friday thereafter.

 (2) The winter quarter shall begin on the first Monday after January 1 and end on the eleventh Friday thereafter. When January 1 falls on Sunday, the winter quarter shall begin on Tuesday January 3; when January 1 falls on Monday, the winter quarter shall begin on Wednesday January 3.

 (3) The spring quarter shall begin on the second Monday after the close of winter quarter and end on the eleventh Friday thereafter. The June commencement shall be the Saturday immediately following the last day of spring quarter.

 (4) The summer quarter shall begin on the second Monday following the June commencement and end on the ninth Friday thereafter.

 (5) Certain academic programs may begin or end on schedules different from those in subsections (1) through (4) of this section with the approval of the provost. In such cases, it will be the responsibility of the appropriate dean to provide advance notice to the affected students.

 [Statutory Authority: RCW 28B.20.130. 03-08-040,  478-132-030, filed 3/27/03, effective 4/1/04; 00-04-038,  478-132-030, filed 1/25/00, effective 2/25/00. Statutory Authority: RCW 28B.20.130(1). 80-03-049 (Order 79-7),  478-132-030, filed 2/22/80; Order 72-10,  478-132-030, filed 11/30/72.]

*/

# academic_calendar(YEAR)
#
#   Return an anonymous hash, keyed on quarter (1,2,3,4), with fields
#   'start', 'end', 'commencement'.

function academic_calendar($year) {
  $mydate = new DateTime();

# The winter quarter shall begin on the first Monday after January 1
# and end on the eleventh Friday thereafter.
# When January 1 falls on Sunday, the winter quarter shall begin on
# Tuesday January 3; when January 1 falls on Monday, the winter
# quarter shall begin on Wednesday January 3.

  $academic_calendar{1}{'quarter'} = 'Winter';
  $mydate->setDate($year, 1, 1);
  if ($mydate->format('w') == 0 || $mydate->format('w') == 1) {
    $mydate->setDate($year, 1, 3);
  } else {
    $mydate->modify('+1 Monday');
  }
  $academic_calendar{1}{'start'} = $mydate->format('Y-m-d');

#...and end on the eleventh Friday thereafter.

  $mydate->modify('+11 Friday');
  $academic_calendar{1}{'end'} = $mydate->format('Y-m-d');

#The spring quarter shall begin on the second Monday after the close of winter quarter

  $academic_calendar{2}{'quarter'} = 'Spring';
  $mydate->modify('+2 Monday');
  $academic_calendar{2}{'start'} = $mydate->format('Y-m-d');

#...and end on the eleventh Friday thereafter.

  $mydate->modify('+11 Friday');
  $academic_calendar{2}{'end'} = $mydate->format('Y-m-d');

#The June commencement shall be the Saturday immediately following the last day of spring quarter.

  $mydate->modify('+1 Saturday');
  $academic_calendar{2}{'commencement'} = $mydate->format('Y-m-d');

#The summer quarter shall begin on the second Monday following the June commencement

  $academic_calendar{3}{'quarter'} = 'Summer';
  $mydate->modify('+2 Monday');
  $academic_calendar{3}{'start'} = $mydate->format('Y-m-d');

#...and end on the ninth Friday thereafter.

  $mydate->modify('+9 Friday');
  $academic_calendar{3}{'end'} = $mydate->format('Y-m-d');

#The autumn quarter shall begin on the last Wednesday in September

  $mydate = new DateTime; #reusing is buggy?
  $academic_calendar{4}{'quarter'} = 'Autumn';
  $mydate->setDate($year, 10, 1);
  $mydate->modify('-1 Wednesday');
  $academic_calendar{4}{'start'} = $mydate->format('Y-m-d');

#...and end on the twelfth Friday thereafter.

  $mydate->modify('+12 Friday');
  $academic_calendar{4}{'end'} = $mydate->format('Y-m-d');



#exceptions:
  switch($year) {
  case 2012:
    $academic_calendar{4}{'start'} = '2012-09-24';
    break;
  }


  return $academic_calendar;

}


function academic_quarter($date = 'now') {
    $date = new DateTime($date);

    for ($year = $date->format('Y'); ; $year++) {
	$calendar = academic_calendar($year);
#	print_r($calendar);
	for ($quarter = 1; $quarter <= 4; $quarter++) {
	    if ( $date->format('Y-m-d') < $calendar{$quarter}{'end'} ) {
		return array($year, $quarter);
	    }
	}
    }
}


?>
