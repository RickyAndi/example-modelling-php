<?php

namespace app\tests;

use app\models\Skp;
use app\models\SkpItem;
use app\models\SkpItemMilestone;
use app\models\PenilaianBulanan;
use app\models\PeriodeParameter;
use PHPUnit\Framework\TestCase;

class SkpTest extends TestCase
{
    public $skp;

    public function __construct()
    {
        $this->skp = new Skp();

        $skpItemMilestones1 = [
            new SkpItemMilestone(1),
            new SkpItemMilestone(2),
            new SkpItemMilestone(3),
            new SkpItemMilestone(4),
            new SkpItemMilestone(5),
            new SkpItemMilestone(6),
            new SkpItemMilestone(7),
            new SkpItemMilestone(8),
            new SkpItemMilestone(9),
            new SkpItemMilestone(10),
            new SkpItemMilestone(11),
            new SkpItemMilestone(12)
        ];

        $skpItemMilestones2 = [
            new SkpItemMilestone(1),
            new SkpItemMilestone(2),
            new SkpItemMilestone(3),
            new SkpItemMilestone(4),
            new SkpItemMilestone(5),
            new SkpItemMilestone(6),
            new SkpItemMilestone(7),
            new SkpItemMilestone(8),
            new SkpItemMilestone(9),
            new SkpItemMilestone(10),
            new SkpItemMilestone(11),
            new SkpItemMilestone(12)
        ];

        $skpItem1 = new SkpItem('satu');
        $skpItem2 = new SkpItem('dua');

        $skpItem1->setSkpItemMilestones($skpItemMilestones1);
        $skpItem2->setSkpItemMilestones($skpItemMilestones2);

        $this->skp->setSkpItems([$skpItem1, $skpItem2]);

        $penilaianBulanan_1 = new PenilaianBulanan(1);
        $penilaianBulanan_1->makeAsAlreadyMarked();

        $penilaianBulanan_2 = new PenilaianBulanan(2);
        $penilaianBulanan_2->makeAsAlreadyMarked();
        
        $penilaianBulanans = [
            $penilaianBulanan_1,
            $penilaianBulanan_2
        ];

        $this->skp->setPenilaianBulanans($penilaianBulanans);
    }

    public function testSkpCanBeInstantiated()
    {
        $this->assertNotEquals($this->skp, null);
    }

    public function testSkpCanGetAvailableMilestoneMonths()
    {
        $expectedMilestoneMonths = [1,2,3,4,5,6,7,8,9,10,11,12];
        $availableMilestoneMonths = $this->skp->getAvailableMilestoneMonths();
        $this->assertEquals($availableMilestoneMonths, $expectedMilestoneMonths);
    }

    public function testSkpCanDetermineWhetherSpecificMonthExistsInMilestoneMonths()
    {
        $isMonthExistsInMilestoneMonths = $this->skp->isMonthHavePenilaian(1);
        $this->assertEquals($isMonthExistsInMilestoneMonths, true);
    }

    public function testSkpCanDetermineWhetherSpecificMonthNotExistsInMilestoneMonths()
    {
        $isMonthExistsInMilestoneMonths = $this->skp->isMonthHavePenilaian(13);
        $this->assertEquals($isMonthExistsInMilestoneMonths, false);
    }

    public function testSkpCanAddPenilaianBulanan()
    {
        $penilaianBulanan = new PenilaianBulanan(3);
        $this->skp->addPenilaianBulanans($penilaianBulanan);

        $this->assertEquals(3, count($this->skp->getPenilaianBulanans()));
    }

    public function testSkpCanKnowWhetherSpecificMonthHavePenilaian()
    {
        $doHavePenilaian = $this->skp->hasPenilaianForSpecificMonth(1);
        $this->assertEquals(true, $doHavePenilaian);
    }

    public function testSkpCanGetPenilaianForSpecificMonth()
    {
        $penilaian = $this->skp->getPenilaianForSpecificMonth(1);
        $this->assertEquals($penilaian->getBulan(), 1);
    }

    public function testSkpCanKnowWhetherSpecificMonthDoNotHavePenilaian()
    {
        $doHavePenilaian = $this->skp->hasPenilaianForSpecificMonth(12);
        $this->assertEquals(false, $doHavePenilaian);
    }

    public function testSkpCanDecideWhichMonthToBeDinilai()
    {
        $periodeRealisasiBulanan = PeriodeParameter::realisasiBulananPeriode(20, 30);
        $tenggangWaktuRealisasiBulanan = PeriodeParameter::tenggangWaktuRealisasiBulanan(5);

        $expectedMonth_1 = 11;
        $monthToBeDinilai_1 = $this->skp->getMilestoneMonthTobeDinilai($periodeRealisasiBulanan, $tenggangWaktuRealisasiBulanan, "2016-12-3");
        
        $expectedMonth_2 = 3;
        $monthToBeDinilai_2 = $this->skp->getMilestoneMonthTobeDinilai($periodeRealisasiBulanan, $tenggangWaktuRealisasiBulanan, "2016-4-1");

        $expectedMonth_3 = 4;
        $monthToBeDinilai_3 = $this->skp->getMilestoneMonthTobeDinilai($periodeRealisasiBulanan, $tenggangWaktuRealisasiBulanan, "2016-5-1");

        $penilaianBulanan_3 = new PenilaianBulanan(3);
        $penilaianBulanan_3->makeAsAlreadyMarked();
        $this->skp->addPenilaianBulanans($penilaianBulanan_3);
        $expectedMonth_4 = 4;
        $monthToBeDinilai_4 = $this->skp->getMilestoneMonthTobeDinilai($periodeRealisasiBulanan, $tenggangWaktuRealisasiBulanan,  "2016-4-21");

        $penilaianBulanan_6 = new PenilaianBulanan(4);
        $penilaianBulanan_6->makeAsAlreadyMarked();
        $this->skp->addPenilaianBulanans($penilaianBulanan_6);
        $expectedMonth_6 = null;
        $monthToBeDinilai_6 = $this->skp->getMilestoneMonthTobeDinilai($periodeRealisasiBulanan, $tenggangWaktuRealisasiBulanan,  "2016-4-21");

        $penilaianBulanan_4 = new PenilaianBulanan(5);
        $penilaianBulanan_4->makeAsAlreadyMarked();
        $this->skp->addPenilaianBulanans($penilaianBulanan_4);
        $expectedMonth_5 = null;
        $monthToBeDinilai_5 = $this->skp->getMilestoneMonthTobeDinilai($periodeRealisasiBulanan, $tenggangWaktuRealisasiBulanan, "2016-5-20");
        
        $this->assertEquals($expectedMonth_1, $monthToBeDinilai_1);
        $this->assertEquals($expectedMonth_2, $monthToBeDinilai_2);
        $this->assertEquals($expectedMonth_3, $monthToBeDinilai_3);
        $this->assertEquals($expectedMonth_4, $monthToBeDinilai_4);
        $this->assertEquals($expectedMonth_5, $monthToBeDinilai_5);
        $this->assertEquals($expectedMonth_6, $monthToBeDinilai_6);
    }

    public function testIfPreviousMilestoneMonthStillNotDinilaiSoInNextMonthStillGetPreviousMonth()
    {
        $skp = new Skp();
        $periodeRealisasiBulanan = PeriodeParameter::realisasiBulananPeriode(20, 25);
        $tenggangWaktuRealisasiBulanan = PeriodeParameter::tenggangWaktuRealisasiBulanan(7);

        $skpItem = new SkpItem('satu');
        $skpItemMilestones = [
            new SkpItemMilestone(1),
            new SkpItemMilestone(2),
            new SkpItemMilestone(3),
            new SkpItemMilestone(4)
        ];
        $skpItem->setSkpItemMilestones($skpItemMilestones);
        $skp->setSkpItems([$skpItem]);

        $penilaianBulanan = new PenilaianBulanan(1);
        $skp->setPenilaianBulanans([$penilaianBulanan]);

        $expectedMonth = 1;

        $this->assertEquals($expectedMonth, $skp->getMilestoneMonthTobeDinilai($periodeRealisasiBulanan, $tenggangWaktuRealisasiBulanan, "2016-2-1"));

        $penilaianBulanan = new PenilaianBulanan(1);
        $penilaianBulanan->makeAsAlreadyMarked();
        $skp->setPenilaianBulanans([$penilaianBulanan]);

        $expectedMonth = 2;

        $this->assertEquals($expectedMonth, $skp->getMilestoneMonthTobeDinilai($periodeRealisasiBulanan, $periodeRealisasiBulanan, "2016-2-1"));
    }

    public function testIfCurrentMonthNotExistInMilestoneMonthAndPreviousMonthDoNotHavePenilaian()
    {
        $skp = new Skp();
        $periodeRealisasiBulanan = PeriodeParameter::realisasiBulananPeriode(20, 27);
        $tenggangWaktuRealisasiBulanan = PeriodeParameter::tenggangWaktuRealisasiBulanan(5);

        $skpItem = new SkpItem('satu');
        $skpItemMilestones = [
            new SkpItemMilestone(1),
            new SkpItemMilestone(3),
            new SkpItemMilestone(5),
            new SkpItemMilestone(9)
        ];
        $skpItem->setSkpItemMilestones($skpItemMilestones);
        $skp->setSkpItems([$skpItem]);

        $penilaianBulanan = new PenilaianBulanan(1);
        $skp->setPenilaianBulanans([$penilaianBulanan]);

        $expectedMonth = 1;

        $this->assertEquals($expectedMonth, $skp->getMilestoneMonthTobeDinilai($periodeRealisasiBulanan, $periodeRealisasiBulanan, "2016-2-1"));

        $expectedMonth = 3;

        $this->assertEquals($expectedMonth, $skp->getMilestoneMonthTobeDinilai($periodeRealisasiBulanan, $periodeRealisasiBulanan, "2016-4-1"));
    }

    public function testIfTwoMonthsSequentiallyDontHaveMilestoneMonths()
    {
        $skp = new Skp();
        
        $skpItem = new SkpItem('satu');
        $skpItemMilestones = [
            new SkpItemMilestone(1),
            new SkpItemMilestone(4),
            new SkpItemMilestone(5),
            new SkpItemMilestone(6)
        ];
        $skpItem->setSkpItemMilestones($skpItemMilestones);
        $skp->setSkpItems([$skpItem]);

        $penilaianBulanan = new PenilaianBulanan(1);
        $skp->setPenilaianBulanans([$penilaianBulanan]);

        $expectedMonth = null;

        $periodeRealisasiBulanan = PeriodeParameter::realisasiBulananPeriode(20, 25);
        $tenggangWaktuRealisasiBulanan = PeriodeParameter::tenggangWaktuRealisasiBulanan(5);

        $this->assertEquals($expectedMonth, $skp->getMilestoneMonthTobeDinilai($periodeRealisasiBulanan, $tenggangWaktuRealisasiBulanan, "2016-3-20"));
    }

    public function testSkpCanGetMilestoneToBeMarked_scenario_1()
    {
        $skp = new Skp();
        
        $periodeRealisasiBulanan = PeriodeParameter::realisasiBulananPeriode(1, 20);
        $tenggangWaktuRealisasiBulanan = PeriodeParameter::tenggangWaktuRealisasiBulanan(5);

        $skpItem = new SkpItem('satu');
        $skpItemMilestones = [
            new SkpItemMilestone(1),
            new SkpItemMilestone(2),
            new SkpItemMilestone(3),
            new SkpItemMilestone(4)
        ];

        $skpItem->setSkpItemMilestones($skpItemMilestones);
        $skp->setSkpItems([$skpItem]);

        $expectedMonth = 1;
        $this->assertEquals($expectedMonth, $skp->getMilestoneMonthTobeDinilai($periodeRealisasiBulanan, $tenggangWaktuRealisasiBulanan, "2016-1-5"));

        $expectedMonth = 1;
        $this->assertEquals($expectedMonth, $skp->getMilestoneMonthTobeDinilai($periodeRealisasiBulanan, $tenggangWaktuRealisasiBulanan, "2016-1-21"));

        $expectedMonth = null;
        $this->assertEquals($expectedMonth, $skp->getMilestoneMonthTobeDinilai($periodeRealisasiBulanan, $tenggangWaktuRealisasiBulanan, "2016-1-27"));
    }

    public function testSkpCanGetMilestoneToBeMarked_scenario_2()
    {
        $skp = new Skp();
        
        $periodeRealisasiBulanan = PeriodeParameter::realisasiBulananPeriode(20, 25);
        $tenggangWaktuRealisasiBulanan = PeriodeParameter::tenggangWaktuRealisasiBulanan(7);

        $skpItem = new SkpItem('satu');
        $skpItemMilestones = [
            new SkpItemMilestone(1),
            new SkpItemMilestone(2),
            new SkpItemMilestone(3),
            new SkpItemMilestone(4)
        ];

        $skpItem->setSkpItemMilestones($skpItemMilestones);
        $skp->setSkpItems([$skpItem]);

        $expectedMonth = 1;
        $this->assertEquals($expectedMonth, $skp->getMilestoneMonthTobeDinilai($periodeRealisasiBulanan, $tenggangWaktuRealisasiBulanan, "2016-2-1"));

        $expectedMonth = 2;
        $this->assertEquals($expectedMonth, $skp->getMilestoneMonthTobeDinilai($periodeRealisasiBulanan, $tenggangWaktuRealisasiBulanan, "2016-2-5"));

        $penilaianBulanan = new PenilaianBulanan(2);
        $penilaianBulanan->makeAsAlreadyMarked();
        $skp->addPenilaianBulanans($penilaianBulanan);

        $expectedMonth = null;
        $this->assertEquals($expectedMonth, $skp->getMilestoneMonthTobeDinilai($periodeRealisasiBulanan, $tenggangWaktuRealisasiBulanan, "2016-2-6"));
    }

    public function testSkpCanGetMilestoneToBeMarked_scenario_3()
    {
        $skp = new Skp();
        
        $periodeRealisasiBulanan = PeriodeParameter::realisasiBulananPeriode(20, 25);
        $tenggangWaktuRealisasiBulanan = PeriodeParameter::tenggangWaktuRealisasiBulanan(7);

        $skpItem = new SkpItem('satu');
        $skpItemMilestones = [
            new SkpItemMilestone(2),
            new SkpItemMilestone(3),
            new SkpItemMilestone(4)
        ];

        $skpItem->setSkpItemMilestones($skpItemMilestones);
        $skp->setSkpItems([$skpItem]);

        $expectedMonth = null;
        $this->assertEquals($expectedMonth, $skp->getMilestoneMonthTobeDinilai($periodeRealisasiBulanan, $tenggangWaktuRealisasiBulanan, "2016-1-1"));

        $expectedMonth = null;
        $this->assertEquals($expectedMonth, $skp->getMilestoneMonthTobeDinilai($periodeRealisasiBulanan, $tenggangWaktuRealisasiBulanan, "2016-1-21"));

        $expectedMonth = null;
        $this->assertEquals($expectedMonth, $skp->getMilestoneMonthTobeDinilai($periodeRealisasiBulanan, $tenggangWaktuRealisasiBulanan, "2016-1-26"));

        $expectedMonth = 2;
        $this->assertEquals($expectedMonth, $skp->getMilestoneMonthTobeDinilai($periodeRealisasiBulanan, $tenggangWaktuRealisasiBulanan, "2016-2-21"));

        $expectedMonth = 3;
        $this->assertEquals($expectedMonth, $skp->getMilestoneMonthTobeDinilai($periodeRealisasiBulanan, $tenggangWaktuRealisasiBulanan, "2016-3-21"));

        $expectedMonth = 4;
        $this->assertEquals($expectedMonth, $skp->getMilestoneMonthTobeDinilai($periodeRealisasiBulanan, $tenggangWaktuRealisasiBulanan, "2016-4-21"));

        $expectedMonth = null;
        $this->assertEquals($expectedMonth, $skp->getMilestoneMonthTobeDinilai($periodeRealisasiBulanan, $tenggangWaktuRealisasiBulanan, "2016-5-21"));
    }
}
