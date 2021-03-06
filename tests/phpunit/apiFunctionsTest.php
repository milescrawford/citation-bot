<?php

require_once __DIR__ . '/../testBaseClass.php';

final class apiFunctionsTest extends testBaseClass {
  
  public function testAdsabsApi() {
    $this->bibcode_secrets(function() {
      $bibcodes = [
       '2017NatCo...814879F', // 0
       '1974JPal...48..524M', // 1
       '1996GSAB..108..195R', // 2
       '1966Natur.211..116M', // 3
       '1995Sci...267...77R', // 4
       '1995Geo....23..967E', // 5
       '2003hoe..book.....K', // 6
       '2000A&A...361..952H', // 7
       ];
      $text = '{{Cite journal | bibcode = ' . implode('}}{{Cite journal | bibcode = ', $bibcodes) . '}}';
      $page = new TestPage();
      $page->parse_text($text);
      $templates = $page->extract_object('Template');
      $page->expand_templates_from_identifier('bibcode', $templates);
      $this->assertSame('Nature', $templates[3]->get('journal'));
      $this->assertSame('Geology', $templates[5]->get('journal'));
      $this->assertSame('14879', $templates[0]->get('pages'));
      $this->assertNull($templates[6]->get('journal'));
      $this->assertSame('Astronomy and Astrophysics', $templates[7]->get('journal'));
    });
    $text = "fafa3faewf34af";
    $this->assertSame($text, bibcode_link($text)); // Mostly just for code coverage, make sure code does not seg fault.
  }
  
  public function testArxivDateUpgradeSeesDate() {
      $text = '{{Cite journal|date=September 2010|doi=10.1016/j.physletb.2010.08.018|arxiv=1006.4000}}';
      $expanded = $this->process_citation($text);
      $this->assertSame('September 2010', $expanded->get('date'));
      $this->assertNull($expanded->get('year'));
      
      $text = '{{Cite journal|date=September 2009|doi=10.1016/j.physletb.2010.08.018|arxiv=1006.4000}}';
      $expanded = $this->process_citation($text);
      $this->assertNull($expanded->get('date'));
      $this->assertSame('2010', $expanded->get('year'));
  }
  
  public function testExpansion_doi_not_from_crossrefRG() {
     $text = '{{Cite journal| doi= 10.13140/RG.2.1.1002.9609}}';
     $expanded = $this->process_citation($text);
     $this->assertSame('Lesson Study as a form of in-School Professional Development', $expanded->get('title'));
     $this->assertSame('2015', $expanded->get('year'));
     $this->assertSame('Aoibhinn Ni Shuilleabhain', $expanded->get('author1'));
  }
  
    public function testExpansion_doi_not_from_crossrefJapanJournal() {
     $text = '{{cite journal|doi=10.11429/ppmsj1919.17.0_48}}';
     $expanded = $this->process_citation($text);
     $this->assertSame('On the Interaction of Elementary Particles. I', $expanded->get('title'));
     $this->assertSame('1935', $expanded->get('year'));
     $this->assertSame('Proceedings of the Physico-Mathematical Society of Japan. 3Rd Series', $expanded->get('journal'));
     $this->assertSame('17', $expanded->get('volume'));
     $this->assertSame('YUKAWA', $expanded->get('last1'));
     $this->assertSame('Hideki', $expanded->get('first1'));
    }
  // See https://www.doi.org/demos.html  NOT ALL EXPAND AT THIS TIME
  public function testExpansion_doi_not_from_crossrefBook() {
     $expanded = $this->process_citation('{{Cite journal}}');
     expand_doi_with_dx($expanded, '10.1017/CBO9780511983658');  // This is cross-ref doi, so for DX DOI expansion
     $this->assertSame('{{Cite book|year = 1996|isbn = 9780521572903|last1 = Luo|first1 = Zhi-Quan|title = Mathematical Programs with Equilibrium Constraints|last2 = Pang|first2 = Jong-Shi|last3 = Ralph|first3 = Daniel|publisher = Cambridge University Press}}', $expanded->parsed_text());
  }
  
  public function testExpansion_doi_not_from_crossrefBookChapter() {
     $expanded = $this->process_citation('{{Cite journal}}');
     expand_doi_with_dx($expanded, '10.1002/0470841559.ch1');  // This is cross-ref doi, so for DX DOI expansion
     $this->assertSame('{{Cite book|year = 2003|isbn = 0471975141|title = Internetworking LANs and WANs|chapter = Network Concepts|location = Chichester, UK|publisher = John Wiley & Sons}}', $expanded->parsed_text());
  }
  
  public function testExpansion_doi_not_from_crossrefDataCiteSubsets() {
     $expanded = $this->process_citation('{{Cite journal}}');
     expand_doi_with_dx($expanded, '10.1594/PANGAEA.726855');
     $this->assertSame('{{Cite journal|year = 2009|last1 = Irino|first1 = Tomohisa|title = Chemical and mineral compositions of sediments from ODP Site 127-797, supplement to: Irino, Tomohisa; Tada, Ryuji (2000): Quantification of aeolian dust (Kosa) contribution to the Japan Sea sediments and its variation during the last 200 ky. Geochemical Journal, 34(1), 59-93|last2 = Tada|first2 = Ryuji}}', $expanded->parsed_text());
  }

  public function testExpansion_doi_not_from_crossrefDataCiteEarthquake() {
     $expanded = $this->process_citation('{{Cite journal}}');
     expand_doi_with_dx($expanded, '10.1594/GFZ.GEOFON.gfz2009kciu');
     $this->assertSame('{{Cite journal|year = 2009|author1 = Geofon Operator|title = GEOFON event gfz2009kciu (NW Balkan Region)|publisher = Deutsches GeoForschungsZentrum GFZ}}', $expanded->parsed_text());
  }
  
  public function testExpansion_doi_not_from_crossrefDataCiteMappedVisualization() {
     $expanded = $this->process_citation('{{Cite journal}}');
     expand_doi_with_dx($expanded, '10.1594/PANGAEA.667386');
     $this->assertSame('{{Cite book|year = 2008|last1 = Kraus|first1 = Stefan|title = Geological map of Potter Peninsula (King George Island, South Shetland Islands, Antarctic Peninsula)|last2 = del Valle|first2 = Rodolfo|publisher = PANGAEA - Data Publisher for Earth & Environmental Science|chapter = Impact of climate induced glacier melt on marine coastal systems, Antarctica (IMCOAST/IMCONet)}}', $expanded->parsed_text());
  }

  public function testExpansion_doi_not_from_crossrefDataCitevideo() {
     $expanded = $this->process_citation('{{Cite journal}}');
     expand_doi_with_dx($expanded, '10.3207/2959859860');
     $this->assertSame('{{Cite journal|year = 2009|last1 = Kirchhof|first1 = Bernd|title = Silicone oil bubbles entrapped in the vitreous base during silicone oil removal}}', $expanded->parsed_text());
  }

  public function testExpansion_doi_not_from_crossre_fISTIC_Journal() {
     $expanded = $this->process_citation('{{Cite journal}}');
     expand_doi_with_dx($expanded, '10.3866/PKU.WHXB201112303');
     $this->assertSame('{{Cite journal}}', $expanded->parsed_text());
  }
  
  public function testExpansion_doi_not_from_crossre_fISTIC_Data() {
     $expanded = $this->process_citation('{{Cite journal}}');
     expand_doi_with_dx($expanded, '10.3972/water973.0145.db');
    $this->assertSame('{{Cite journal}}', $expanded->parsed_text());
  }
 
  public function testExpansion_doi_not_from_crossref_ISTIC_Thesis() {
     $expanded = $this->process_citation('{{Cite journal}}');
     expand_doi_with_dx($expanded, '10.7666/d.y351065');
     $this->assertSame('{{Cite journal}}', $expanded->parsed_text());
  }

  public function testExpansion_doi_not_from_crossref_JaLC_Journal() {
     $expanded = $this->process_citation('{{Cite journal}}');
     expand_doi_with_dx($expanded, '10.11467/isss2003.7.1_11');
     $this->assertSame('{{Cite journal|year = 2009|volume = 7|last1 = 竹本|first1 = 賢太郎|title = 大学におけるWebメールとターミナルサービスの研究|last2 = 川東|first2 = 正美|last3 = 久保|first3 = 信行|last4 = 左近|first4 = 多喜男|publisher = 標準化研究学会}}',$expanded->parsed_text());
  }

  public function testExpansion_doi_not_from_crossref_JaLC_Journal2() {
     $expanded = $this->process_citation('{{Cite journal}}');
     expand_doi_with_dx($expanded, '10.7875/leading.author.2.e008');
     $this->assertSame('{{Cite journal|year = 2013|volume = 2|last1 = 川崎|first1 = 努.|title = 植物における免疫誘導と病原微生物の感染戦略|journal = 領域融合レビュー}}', $expanded->parsed_text());
  }

  public function testExpansion_doi_not_from_crossref_mEDRA_Journal() {
     $expanded = $this->process_citation('{{Cite journal}}');
     expand_doi_with_dx($expanded, '10.1430/8105');
     $this->assertSame("{{Cite journal|year = 2002|issue = 4|author1 = Romano Prodi|title = L'Industria dopo l'euro|journal = L'Industria}}", $expanded->parsed_text());
  }
  
  public function testExpansion_doi_not_from_crossref_mEDRA_Monograph() {
     $expanded = $this->process_citation('{{Cite journal}}');
     expand_doi_with_dx($expanded, '10.1392/BC1.0');
     $this->assertSame('{{Cite journal}}', $expanded->parsed_text());
  }     

  // http://doi.airiti.com/
  public function testExpansion_doi_not_from_crossref_airiti_journal() {
     $expanded = $this->process_citation('{{Cite journal}}');
     expand_doi_with_dx($expanded, '10.6620/ZS.2018.57-30');
     $this->assertSame('{{Cite journal}}', $expanded->parsed_text());
  }    
  // http://www.eidr.org/
  public function testExpansion_doi_not_from_crossref_eidr_Black_Panther_Movie() {
     $expanded = $this->process_citation('{{Cite journal}}');
     expand_doi_with_dx($expanded, '10.5240/7B2F-ED76-31F6-8CFB-4DB9-M');
     $this->assertSame('{{Cite journal}}', $expanded->parsed_text());
  } 
 
  // http://www.kisti.re.kr/eng/
  public function testExpansion_doi_not_from_crossref_kisti_journal() {
     $expanded = $this->process_citation('{{Cite journal}}');
     expand_doi_with_dx($expanded, '10.3743/KOSIM.2011.28.2.117');
     $this->assertSame('{{Cite journal|year = 2011|issue = 2|volume = 28|last1 = Kim|first1 = Byung-Kyu|title = Kscd를 활용한 국내 과학기술자의 해외 학술지 인용행태 연구|journal = 정보관리학회지|last2 = Kang|first2 = Mu-Yeong|last3 = Choi|first3 = Seon-Heui|last4 = Kim|first4 = Soon-Young|last5 = You|first5 = Beom-Jong|last6 = Shin|first6 = Jae-Do}}', $expanded->parsed_text());
  } 
  
  // https://publications.europa.eu/en/
  public function testExpansion_doi_not_from_crossref_europa_monograph() {
     $expanded = $this->process_citation('{{Cite journal}}');
     expand_doi_with_dx($expanded, '10.2788/14231');
     $this->assertSame('{{Cite journal}}', $expanded->parsed_text());
  } 
  
  public function testComplexCrossRef() {
     $text = '{{citation | title = Deciding the Winner of an Arbitrary Finite Poset Game is PSPACE-Complete| arxiv = 1209.1750| bibcode = 2012arXiv1209.1750G}}';
     $expanded = $this->process_citation($text);
     $this->assertSame('Deciding the Winner of an Arbitrary Finite Poset Game is PSPACE-Complete', $expanded->get('chapter'));
     $this->assertSame('Lecture Notes in Computer Science', $expanded->get('series'));
     $this->assertSame('Automata, Languages, and Programming', $expanded->get('title'));
  }
  
   public function testThesisDOI() {
     $doi = '10.17077/etd.g638o927';
     $text = "{{cite journal|doi=$doi}}";
     $template = $this->make_citation($text);
     expand_doi_with_dx($template, $doi);
     $this->assertSame('10.17077/etd.g638o927', $template->get('doi'));
     $this->assertSame("The caregiver's journey", $template->get('title'));
     $this->assertSame('The University of Iowa', $template->get('publisher'));
     $this->assertSame('2018', $template->get('year'));
     $this->assertSame('Schumacher', $template->get('last1')); 
     $this->assertSame('Lisa Anne', $template->get('first1'));
  }
  
   public function testJstor1() {
     $text = "{{cite journal|url=https://jstor.org/stable/832414?seq=1234}}";
     $template = $this->make_citation($text);
     $this->assertTrue(expand_by_jstor($template));
     $this->assertNull($template->get('jstor')); // We don't do that here
   }
  
   public function testJstor2() {
     $text = "{{cite journal|jstor=832414?seq=1234}}";
     $template = $this->make_citation($text);
     $this->assertTrue(expand_by_jstor($template));
     $this->assertNull($template->get('url'));
   }
  
   public function testJstor3() {
     $text = "{{cite journal|jstor=123 123}}";
     $template = $this->make_citation($text);
     $this->assertFalse(expand_by_jstor($template));
   }
  
   public function testJstor4() {
     $text = "{{cite journal|jstor=i832414}}";
     $template = $this->make_citation($text);
     $this->assertFalse(expand_by_jstor($template));
   }
  
   public function testJstor5() {
     $text = "{{cite journal|jstor=4059223|title=This is not the right title}}";
     $template = $this->make_citation($text);
     $this->assertFalse(expand_by_jstor($template));
     $this->assertSame($text, $template->parsed_text());
  }
  
  public function testArxivChemicals() {
     $text = "{{cite arXiv|eprint=2001.01484}}";
     $template = $this->process_citation($text);
     $this->assertSame("<sup>14</sup>N/<sup>15</sup>N isotopic ratio in CH<sub>3</sub>CN of Titan's atmosphere measured with ALMA", $template->get('title'));
  }

}
