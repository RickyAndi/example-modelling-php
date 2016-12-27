Feature: Feature
	Jancok
	Jancok
	Jancok

	Scenario: Jancok
		When I add the skpItem to the skp with nama jancok
		Then the number of skpitem should be 1
		When I add the skpItem to the skp with nama gendeng
		Then the number of skpitem should be 2
