-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2017. Nov 04. 13:32
-- Kiszolgáló verziója: 10.1.16-MariaDB
-- PHP verzió: 5.6.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `brit`
--

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `brit_articles`
--
-- használatban(#1932 - Table 'brit.brit_articles' doesn't exist in engine)
-- Hiba az adatolvasás közben: (#1932 - Table 'brit.brit_articles' doesn't exist in engine)

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `brit_articles_2`
--

CREATE TABLE `brit_articles_2` (
  `cikk_id` int(11) NOT NULL,
  `cikk_title` varchar(300) COLLATE utf8_hungarian_ci NOT NULL,
  `cikk_titlealias` varchar(300) COLLATE utf8_hungarian_ci NOT NULL,
  `cikk_createdate` datetime NOT NULL,
  `cikk_modifydate` datetime NOT NULL,
  `cikk_author` int(11) NOT NULL,
  `cikk_modifyauthor` int(11) NOT NULL,
  `cikk_content` mediumtext COLLATE utf8_hungarian_ci NOT NULL,
  `cikk_visible` float NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `brit_articles_2`
--

INSERT INTO `brit_articles_2` (`cikk_id`, `cikk_title`, `cikk_titlealias`, `cikk_createdate`, `cikk_modifydate`, `cikk_author`, `cikk_modifyauthor`, `cikk_content`, `cikk_visible`) VALUES
(3, 'Szex mindenkinek!', 'Szex_mindenkinek', '2017-09-13 09:09:57', '2017-09-13 12:09:48', 1, 0, '<p>Ölbe vettem és felvittem az emeletre. A franciaágyra "dobtam", széthúztam a combjait,és őrűlt sebeséggel nyalni kezdtem.surprised Ő csak nyőgdécselt majd mielőtt a csúcsra ért volna abbahagytam, elő vettem egy óvszert gyorsan legörgettem az álló farkamon,felémásztam és kínzó lassúsággal feltoltam neki.†. felszisszent ugyan de aztmondta nincs gond. Hagytam hogy szokja egy kicsit majd kivettem belőle leszaladtam egy kötel helyettesítésére alkalmas eszközért,találtam is egy hosszú selyemszallagot. -Ez tökéletes lesz gondoltam. Visszamentem hozzá.♠♣<span style="color: #ff0000;">♥♦</span></p>\r\n<p><span style="font-size: 14pt;"><img style="border: 10px ridge pink; border-width: 6px;" src="http://localhost/brit/img/Alexis-Adams-naughty-america-12.jpg" alt="Alexis-Adams" width="267" height="400" /></span> <span style="font-size: 14pt;"><img style="border: 10px ridge pink; border-width: 6px;" src="http://localhost/brit/img/alana145.JPG" alt="Alana Soares" width="306" height="400" /> <img style="border: 10px ridge pink; border-width: 6px;" src="http://localhost/brit/img/0097.JPG" height="400" /><br /></span></p>\r\n<p> Csak egy kis módosítás.</p>', 1),
(4, 'Sztárok, akikről nem tudtad, hogy leszbikusak: Michelle Rodrigueztől Jennifer Hudsonig', 'Sztarok-_akikrol_nem_tudtad-_hogy_leszbikusak-_Michelle_Rodrigueztol_Jennifer_Hudsonig', '2017-09-13 12:09:56', '2017-09-16 08:09:15', 1, 0, '<p style="text-align: justify;"><strong>Egyes sztárok felvállalják, ha esetleg a másik nemhez (is) vonzódnak, mások pedig igyekszenek ezt titokban tartani, teljesen felelegesen. Sajnos az ismertség átka, hogy a palástolni próbált dolgaik mindig kiderülnek, így az is, ha a világhírű hölgyeket nem egy férfi, hanem egy másik nő hozza lázba. Íme, azok a hírességek, akikről nem tudhattuk eddig, hogy leszbikusak!</strong></p>\r\n<p style="text-align: justify;"> </p>\r\n<h2 style="text-align: justify;">Michelle Rodriguez</h2>\r\n<p style="text-align: justify;"> </p>\r\n<p><span class="p-mega"><img style="display: block; margin-left: auto; margin-right: auto;" src="http://localhost/brit/img/20160406michelle-rodriguez-halalos-iramban.jpg" height="400" /></span></p>\r\n<p> </p>\r\n<p style="text-align: justify;">Na jó, talán róla lehetett sejteni, hiszen a filmjeiben sem éppen szende, megmentésre váró királykisasszonyokat formált meg, de ha volt valakinek kétsége efelől, akkor eloszlatjuk. Michelle Rodrigez, akit a legtöbben a Halálos iramban című filmekből ismerünk bizony leszbikus, méghozzá azért, mert nagyon kíváncsi. Kipróbált márt ezt is, azt is, de a nők iránt érdeklődik a leginkábbb.</p>\r\n<p style="text-align: justify;"> </p>\r\n<h2>Stella Maxwell</h2>\r\n<p> </p>\r\n<p><img style="display: block; margin-left: auto; margin-right: auto;" src="http://localhost/brit/img/20170904stella-maxwell.jpg" height="400" /></p>\r\n<p> </p>\r\n<p>Sok pasi áhítozik a szépséges modellért, de sajnos azt kell mondanunk, hogy hiába. Önerőből jutott a karrierje csúcsára, és egyéb csúcsokat sem a pasik segítségével hódít meg. Már több leszbikus kapcsolatáról is pletykáltak, jelenleg Kristen Stewart színésznővel van nagyon közeli viszonyban.</p>\r\n<p> </p>\r\n<h2>Maria Bello</h2>\r\n<p> </p>\r\n<p><span class="p-mega"><img style="display: block; margin-left: auto; margin-right: auto;" src="http://localhost/brit/img/20170728horizontal.jpg" height="400" /></span></p>\r\n<p> </p>\r\n<p>A szépséges színésznő nem szereti a korlátokat, így azt sem, ha előre meghatározzák, neki melyik nemet kell szeretnie. Maria legismertebb filmje a Sakáltanya, de nem ez az egyetlen alkotás, amiben játszott. Ő már nincsen annyira a figyelem középpontjában, ezért nem is hallhattunk annyit szexuális beállítottságáról, pedig ő nyíltan vállalja, hogy volt már leszbikus kapcsolata.</p>\r\n<p> </p>\r\n<h2>Jennifer Hudson</h2>\r\n<p> </p>\r\n<p><span class="p-mega"><img style="display: block; margin-left: auto; margin-right: auto;" src="http://localhost/brit/img/20170327tvsztarok-jennifer-hudson-megmutatta-hosszu3.jpg" height="400" /></span></p>\r\n<p> </p>\r\n<p>Az énekesnő az American Idol 2004-es évadában vált ismertté, azóta is folyamatosan szerepel, koncertezik. Jennifer erős nőként küzdötte fel magát az ismertség legfelső fokára, de a szexuális beállítottságára csak kétértelmű utalásokat tesz. Egyesek a dalszövegeiben elfojtott leszbikus vágyakat véltek felfedezni, és természetesen az énekesnőnek is szegezték a kérdést, aki homályosan fogalmazott, de határozottan nem tagadta a felvetést.</p>\r\n<p> </p>\r\n<h2>Samantha Fox</h2>\r\n<p> </p>\r\n<p><span class="p-mega"><img style="display: block; margin-left: auto; margin-right: auto;" src="http://localhost/brit/img/20160701samantha-fox-regen-es-most1.jpg" height="400" /></span></p>\r\n<p> </p>\r\n<p>Igazi szexszimbólumnak számított Samantha, a maga korában az egyik leghíresebb és a legáhítottabb nő volt. Folyton az újságok címlapján szerepelt, és nem is kellett sokáig várni arra, hogy elinduljanak a találgatások. Samantha ugyanis soha nem mutatkozott férfiak oldalán, pedig neki aztán volt kiből válogatni. Kisvártatva végül beismerte, hogy inkább nőkkel osztja meg az ágyát.</p>\r\n<p style="text-align: justify;"> </p>', 1),
(5, 'Nem forgathattak leszbikusokról a katolikus iskolában', 'Nem_forgathattak_leszbikusokrol_a_katolikus_iskolaban', '2017-09-17 08:09:16', '2017-09-17 08:09:00', 1, 0, '<p>A Julianne Moore és Ellen Page főszereplésével készülő Freeheld egy dokumentumfilmen alapul, amiben egy idősebb, halálos beteg rendőrnő beleszeret egy fiatalabb lányba. Egy jelenetben Moore karaktere a helyi önkormányzatnál hivatalos élettársi viszonyt akar kérvényezni, hogy a nyugdíját Page karaktere kaphassa meg. Ezt a jelenetet akarták a szalézi rend egyik középiskolájában leforgatni a New York állambeli New Rochelle-ben.</p>\r\n<p><a id="hyperlink_ee3747d90ac00000e2b62bbb84e30c7b" href="http://dex.hu/x.php?id=index_kultur_cikklink&amp;url=http%3A%2F%2Fwww.hollywoodreporter.com%2Fnews%2Fjulianne-moore-ellen-pages-lesbian-742670" target="_blank" rel="noopener">A Hollywood Reporter úgy tudja</a>, hogy a helyszínválasztásnál a film munkatársai bejárhatták az épületet és fotókat is készíthettek, úgy, hogy az iskola dolgozói tudták, miről fog szólni a film. Az iskola igazgatója jóváhagyta a forgatást. Majd nem sokkal később értesítette a film producerét, Michael Shamberget (Ponyvaregény, Django elszabadul), hogy sajnos mégsem szeretnék bevállalni.</p>\r\n<p><img style="float: left; margin: 5px;" src="http://localhost/brit/img/7031599_c598dd9faea28b2899326a589432ac78_wm.jpg" height="180" />Az igazgató őszintén megmondta, hogy a téma miatt. Az iskolába korábban forgattak már videoklipet és reklámot is, de a leszbikus tematika miatt a Freeheldet nem engedték. Shamberg a visszautasítás után levelet írt az igazgatónak, amiben taglalta, hogy a film nem a melegházasságról szól, hanem egy bátor közmunkásról és az ő tisztességéről. A producer azt is hozzátette a levélben, hogy Ferenc pápa is nemrég a melegek elfogadásáról beszélt. Az igazgató megígérte, hogy továbbítja a szöveget az iskola vezetőjének, de onnantól kezdve minden kapcsolat megszakadt.</p>\r\n<p>Az igazgató elmondta a Hollywood Reporternek, hogy az iskola több ügy mellett is kiállt már, ilyen a szegénység, az éhezés vagy a hajléktalanok kérdése. A forgatást viszont nem engedték. Az utolsó pillanatban a jelenetet sikerült áttenni egy közeli kisváros polgármesteri hivatalába.</p>\r\n<p>A Freeheld egy 2007-es, azonos című dokumentumfilm alapján készül. A játékfilmet Peter Sollett (Dalok ismerkedéshez) rendezi, szerepel még benne Steve Carell is.</p>\r\n<p> </p>', 1),
(8, 'Miért buknak a pasik a kívánatos anyukákra?', 'Miert_buknak_a_pasik_a_kivanatos_anyukakra', '2017-09-22 17:09:24', '2017-09-22 17:09:09', 1, 0, '<p style="text-align: justify;"><strong>Tudod, mit jelent a MILF és a MILF Hunting kifejezés? Biztosan vannak köztetek olyanok, akik már hallottak róla, de olyanok is, akik el sem tudják képzelni, mit jelenthet ez a mozaikszó. A MILF azaz "Mother I Like to Fuck" egy angol rövidítés, magyarul finoman fordítva ''kívánatos anyukát'' jelent.</strong></p>\r\n<p style="text-align: justify;"> </p>\r\n<p style="text-align: justify;">Manapság a fiatal, 16-28 éves pasik túlnyomó része nem utasítana vissza egy rendesen karbantartott 30-50-es hölgyet. A kalandvágyó fiataloknak igenis van lehetősége beleakadni egy-egy "fekete özvegy" hálójába. Persze, pénzért mindent lehet, de a történet itt kettéválik.</p>\r\n<p style="text-align: justify;">Az senkinek sem meglepő, hogy a legősibb szakma elhivatott képviselői között is akad jó pár, aki a harmadik, negyedik x-en is túl van. Ez mindig is így volt, és soha nem is lesz másképp. Azonban napjainkban egyre gyakrabban fordul elő, hogy bármiféle anyagi ellenszolgáltatás nélkül is kapcsolatba kerülhet valaki egy csinos anyukával. Közösségi oldalak, chatprogramok, webkamera, társkeresők - elég széles ahhoz a paletta, hogy mindenki találjon magának egy-két zamatos, "érett gyümölcsöt".</p>\r\n<p style="text-align: justify;">Ha jobban belegondolunk, a jelenséget már évekkel ezelőtt láthattunk egy  tini-vígjátékban, az  Amerikai pitében. Nem, nem most nem a pités fiú lesz a lényeg, hanem Stiffler mamája! A történet hasonló volt, mint a való életben. A baráti társaságból az egyik srác kapott az alkalmon, és egy billiárdasztalon óriásit szexelt a szilikoncicis anyukával. Ez a MILF kultuszban sincs másképp, hiszen a fiatal hímek nem komoly kapcsolatot keresnek az ilyen hölgyek körében, pusztán könnyed kikapcsolódást, szexet.</p>\r\n<p style="text-align: justify;">Tehát mitől is lesz valaki MILF? Először is legalább 35 éves, csinos, ápolt, erőteljes szexuális kisugárzással rendelkezik, és ami a legfontosabb, hogy kedvelje a nála jóval fiatalabb hímnemű egyedeket. Bizonyára most sok hölgytársam töri a fejét azon, hogy mi történt a világgal, hiszen minden arról szólt eddig, hogy a pasik a zsenge 18-25 éves korosztályból válogatnak. Először is, egy 40 év körüli nő, főleg ha már elvált, nem valószínű, hogy egyből élete nagy szerelmét keresi. Fontos szempont lehet, hogy a fiatalabb korosztállyal ellentétben az érettebb nők nem támasztanak akkora elvárásokat, és nem is olyan hisztisek, mint fele annyit megélt nőtársaik. Ők már kiélték magukat, nem akarnak állandóan bulizni, csak egy kicsit lazítani valakivel.</p>\r\n<p style="text-align: justify;">Az egyik legfontosabb pluszpont azonban, ami az idősebb korosztály mellett szól, nem más, mint "a rutin meg az évek". Azok a férfiak, akik már létesítettek szexuális kapcsolatot náluk jóval idősebb nőkkel, egybehangzóan azt állítják, hogy ők többet tudnak, volt idejük elég tapasztalatot szerezni.</p>\r\n<p style="text-align: justify;">Ahogy azt már említettem, ezen a vonalon szigorúan anyagi ellenszolgáltatás nélkül zajlanak az események, ezért a hölgyek részéről érdekes kérdés lehet a helyszín. Sok olyan nő van, akinek a státuszából, esetleg az ismeretségi köréből adódóan nem lenne hasznos, ha lebuknának egy gyertyafényes vacsora közben egy 20 éves sráccal. Aztán az sem túl jó, ha a szomszéd Erzsi néni szétkürtöli a fél kerületben, hogy kivel, hol, mikor látott minket. Az ilyen párok számára nagy segítséget jelentenek az úgynevezett titkos házak, melyből az első ilyen kishazánkban több mint egy éve alakult.</p>\r\n<p style="text-align: justify;">Bizony, a MILF-jelenség hazánkban is jócskán felütötte már a fejét, gondoljunk csak a különböző férfimagazinok címlapján pózoló Zalatnay Cinire, Liptai Claudiára és társaikra, és nem lehet véletlen, hogy ezeket a lapszámokat pillanatok alatt elkapkodta a hazai publikum.</p>\r\n<div style="border: 2px solid pink; padding: 10px; text-align: justify;"><span style="font-size: 14pt; border-bottom: 5px;"><strong>Csides Kata szexuálpszichológus szakértőnk véleménye:</strong></span><br />Hát igen, az a bizonyos rutin... Egy 35-40 éves hölgy már nagyon tudja, mit akar az ágyban. Ismeri a saját testét, és ki meri fejezni a kívánságait. Valóban élvezi a szexet, és nem Aida nagybelépőjét adja elő a lepedőn, kamuból. Ma már a férfiak jelentős hányada meg tudja különböztetni, hogy mikor van orgazmusa egy nőnek és mikor nincs. Bár a fiatalok is nagyon "bevállalósak" manapság, de a lányok orgazmushiánya nagyon zavarja a pasikat. (A legtöbb segítségkérés e témában érkezik hozzám.) Az említett korosztálynak - van orgazmusa. Mindenre vevők, és nem akarnak házasságot, gyereket, csak egy laza kapcsolatra, testi örömökre vágynak. Beérnek a nők, és ezt imádják a férfiak. Ezek soha nem komoly kapcsolatok, és nem is tartanak sokáig. Nincs felelősség és nincs elköteleződés. Egy 50 éves férfi mellett egy 20 éves nőt elfogad a társadalom. Az mennyivel jobb?</div>', 1),
(9, '„Úgy néznek rám, mint egy nemi erőszakolóra” - egy ex-pornós élete visszavonulás után', '„Ugy_neznek_ram-_mint_egy_nemi_eroszakolora”_-_egy_ex-pornos_elete_visszavonulas_utan', '2017-10-22 08:10:12', '2017-10-22 13:10:25', 1, 0, '<p> </p>\r\n<p><img style="display: block; margin-left: auto; margin-right: auto;" src="http://localhost/brit/img/olson2.jpg" width="788" height="500" /></p>\r\n<p>Bree Olson most 30 éves, és öt éve nem találja a helyét a világban. Ezt pedig annak tudja be, hogy 25 esztendősen felhagyott a pornózással. A nő a Real Women Real Stories (Igazi nők, igazi történetek) filmes projektben vett részt és mondta el a történetét. Érdekes módon nem kifejezetten a pénzhiány miatt tartja hibának, hogy visszavonult a <a id="hyperlink_340c157d8c3180c5e27e66368ab86a40" title="„Olyan heves volt, hogy kitépte a póthajam” - 5 pornós legkellemetlenebb forgatása" href="http://dex.hu/x.php?id=velvet_elet_cikklink&amp;url=http%3A%2F%2Fvelvet.hu%2Felet%2F2017%2F02%2F25%2Folyan_heves_volt_hogy_kitepte_a_pothajam_-_5_pornos_legkellemetlenebb_forgatasa%2F" target="_blank" rel="noopener" data-recommendation="jobb_hasab" data-recommendation-preview="{" data-recommendation-id="3809447">felnőttfilmezéstől</a>, hanem valami sokkal nyomasztóbbal küzd meg minden nap. Azzal, ahogy a társadalom mikén viszonyul az ex-pornósokhoz.<br />Olson 19 éves volt, mikor elkezdte a szakmát. Elmondása szerint nem csak jó pénzkereseti lehetőség volt, de a saját szexualitásának határait is feszegethette benne. Aztán hat év munka után úgy érezte, elég volt. Úgy gondolta, eljött az idő, hogy klasszikus amerikai, kisvárosi életet éljen, csak azzal nem számolt, hogy erre már neki soha nem lesz lehetősége.</p>\r\n<p> </p>\r\n<h3>„Úgy néznek rám, mint egy nemi erőszaktevőre”</h3>\r\n<p> </p>\r\n<p>A nő arról számol be, hogy múltja miatt a társadalom megbélyegzi és abszolút nem fogadja már be.  </p>\r\n<p> </p>\r\n<blockquote>\r\n<p>Úgy tekintenek rám az emberek, mint egy szexuális erőszaktevőre. Úgy bánnak velem, mintha kevesebb lennénk mint mások.</p>\r\n</blockquote>\r\n<p> </p>\r\n<p>És arra is rá kellett jönnöm, hogy soha nem lehet belőlem ápolónő, nem dolgozhatok gyerekekkel és nem vesznek fel egyetlen olyan céghez sem, ahol erkölcsi kikötések is vannak a szerződésben" - magyarázza az egykori felnőttfilmes, akinek ma már gondjai vannak a megélhetéssel is.</p>\r\n<p><img style="float: left; margin: 0 10px 10px 0;" src="http://localhost/brit/img/olson.jpg" width="300" height="318" /></p>\r\n<h3>A kirekesztettség teljes bezártsághoz vezetett</h3>\r\n<p>Olson arról is mesél az interjúban, mennyire kihat a mindennapjaira is ez kirekesztés.  Teljesen bevett szokás, hogy az emberek lekurvázzák az utcán, sőt van, aki átmegy a túloldalra, hogy ne találkozzon vele szemtől szembe. Ez pedig eljuttatta arra pontra, hogy ha nem muszáj, akkor inkább nem is megy ki a lakásból.</p>\r\n<p>„Életem legnagyobb hibája volt, hogy otthagytam a szakmát, csak azért, hogy elnyerjem az emberek tiszteletét és szeretetét. Ma már tudom, hogy soha nem fognak se megkedvelni, se befogadni” - mondja a nő, aki a fiatal lányoknak is üzen a videóban.</p>\r\n<h3>„Soha ne kezdjetek el pornózni!”</h3>\r\n<p>Ezt mondja a Olson, aki egyébként abszolút nem a pornóipart ítéli el ezzel a kijelentéssel, hanem a közönséget. A nő véleménye az, hogy a felnőttfilmek tényleg sokat segíthetnek a színészeiknek és fogyasztóknak is abban, hogy jobban megismerjék magukat és teljesebb lehessen a szexuális életük, ám még mindig nagyon stigmatizálják <a id="hyperlink_90a73a3d152b0a159c00b161b2d063be" title="Tucatnyi pornós szerepelt már a Trónok harcában" href="http://dex.hu/x.php?id=velvet_elet_cikklink&amp;url=http%3A%2F%2Fvelvet.hu%2Fgumicukor%2F2016%2F06%2F18%2Ftucatnyi_pornos_szerepelt_mar_a_tronok_harcaban%2F" target="_blank" rel="noopener" data-recommendation="jobb_hasab" data-recommendation-preview="{" data-recommendation-id="3638067">az iparág dolgozóit</a>. Amíg pedig ez így van, nem tanácsolná egyetlen fiatal lánynak se, hogy belevágjon.</p>\r\n<blockquote>\r\n<p>A pornózás az egyetlen olyan szakma, amiben egy nő minél sikeresebb, annál többet fog szenvedni miatta a jövőben.</p>\r\n</blockquote>\r\n<p> </p>\r\n<p>És bár vannak sikersztorik, például <a id="hyperlink_3157b49549446149dc10e767ac83ee69" title="Minden, amit tudni akart Aleska Diamondról" href="http://dex.hu/x.php?id=velvet_elet_cikklink&amp;url=http%3A%2F%2Fvelvet.hu%2Fgumicukor%2F2014%2F11%2F07%2Faleska_diamond_csodalatos_elete%2F" target="_blank" rel="noopener">Sáfárny ''Aleska'' Emese</a> , <a id="hyperlink_9f4df2bcec5a22e70563640724c1b44e" title="Hoppá! Michelle Wild hosszú idő után újra kamera elé állt" href="http://dex.hu/x.php?id=velvet_elet_cikklink&amp;url=http%3A%2F%2Fvelvet.hu%2Fgumicukor%2F2015%2F08%2F08%2Fhoppa_michelle_wild_hosszu_ido_utan_ujra_kamera_ele_allt%2F" target="_blank" rel="noopener">Michelle Wild</a> vagy <a id="hyperlink_674fdab6bdeb912364705fe7a872d24e" title="Sasha Grey hosszú lábakkal reklámozza új filmjét" href="http://dex.hu/x.php?id=velvet_elet_cikklink&amp;url=http%3A%2F%2Fvelvet.hu%2Fblogok%2Fgumicukor%2F2014%2F06%2F30%2Fsasha_grey_hosszu_labbakkal_reklamozza_uj_filmjet%2F" target="_blank" rel="noopener">Sasha Grey</a>, akik miután elhagyták a pályát, celebkedni kezdtek. és ez nagyon bejött nekik, de valószínűleg nem ez a jellemző. Ráadásul a szakemberek szerint a férfipornósok még nehezebb helyzetben vannak ebből a szempontból. Erről <a id="hyperlink_7c30db091679bd24c7c08d60bfc8215d" title="„A legnagyobb baj, ha nem áll fel” - A pornósok élete is rámehet az erekcióproblémákra" href="http://dex.hu/x.php?id=velvet_elet_cikklink&amp;url=http%3A%2F%2Fvelvet.hu%2Felet%2F2017%2F03%2F12%2Fa_legnagyobb_baj_ha_nem_all_fel_-_a_pornosok_elete_is_ramehet_az_erekcioproblemakra%2F" target="_blank" rel="noopener" data-recommendation="bekezdes_utan" data-recommendation-preview="{" data-recommendation-id="3823931">itt olvashat bővebben</a>.</p>', 1);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `brit_menu`
--

CREATE TABLE `brit_menu` (
  `menu_id` int(11) NOT NULL,
  `menu_title` varchar(20) COLLATE utf8_hungarian_ci NOT NULL,
  `menu_anchor` varchar(100) COLLATE utf8_hungarian_ci NOT NULL DEFAULT '#',
  `menu_parent_id` int(11) DEFAULT '0',
  `menu_sort` int(11) NOT NULL,
  `menu_admin` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `brit_menu`
--

INSERT INTO `brit_menu` (`menu_id`, `menu_title`, `menu_anchor`, `menu_parent_id`, `menu_sort`, `menu_admin`) VALUES
(1, 'Főoldal', '#', 0, 1, 0),
(2, 'Rólam', 'index.php?page=about', 0, 2, 0),
(3, 'Hírek', 'index.php?page=news', 0, 3, 0),
(4, 'Menu4', '#', 0, 4, 0),
(5, 'Edit Menu', 'index.php?page=editmenu', 0, 5, 0),
(7, 'Egyéb', '#', 0, 7, 0),
(8, 'Történet', '?page=storyes', 10, 2, 0),
(9, 'Present Text', '?page=present_text', 4, 1, 0),
(10, 'Tizedik', '#', 7, 2, 0),
(12, 'Html E-mail teszt', 'email.htm', 4, 3, 0),
(13, 'Tizenh&aacute;rem', 'index.php?page=szavaz', 10, 1, 0),
(14, 'Admin', 'http://localhost/brit/admin', 0, 8, 1),
(15, 'User Data teszt', 'index.php?page=userdatateszt', 4, 5, 0),
(16, 'Hírlevél', 'index.php?page=mail', 4, 6, 0),
(17, 'Sexy', 'index.php?page=sexy', 7, 1, 0),
(18, 'TinyMCE', 'index.php?page=tiny_test', 0, 6, 0),
(19, 'Cikkek listája', 'index.php?page=pages', 4, 9, 0),
(20, 'Belépés', 'index.php?page=login_desk', 1, 2, 0),
(22, 'Regisztráció', 'index.php?page=register', 1, 3, 0),
(23, 'Home', 'index.php', 1, 1, 0);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `brit_news_comment`
--

CREATE TABLE `brit_news_comment` (
  `nwc_id` int(11) NOT NULL,
  `nwc_news` int(11) NOT NULL,
  `nwc_user` int(11) NOT NULL,
  `nwc_date` datetime NOT NULL,
  `nwc_comment` text COLLATE utf8_hungarian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `brit_news_comment`
--

INSERT INTO `brit_news_comment` (`nwc_id`, `nwc_news`, `nwc_user`, `nwc_date`, `nwc_comment`) VALUES
(1, 3, 14, '2017-07-15 12:44:39', 'Nagyon jó hír! Már alig tudom kivárni.'),
(2, 3, 15, '2017-07-15 16:54:24', 'Jó ötletnek tűnik, bár kicsit ódzkodom tőle. Ha más csajokat akarsz nézegetni, ahhoz van számtalan más oldal, olyan is, ami nem fizetős. A vége majd az lesz, hogy mindenféle szeméttel telenyomják ezt az oldalt.'),
(3, 1, 14, '2017-07-30 08:23:02', 'Helló!\r\n\r\nTesztnek elsőre nem rossz, bár én valami britannyst vártam. De ha van még belőlük, akkor jöhet!'),
(5, 1, 1, '2017-07-30 08:33:09', 'Van még és hamarosan fel is lesz töltve, csak légy türelmes.'),
(11, 1, 14, '2017-07-30 08:40:02', 'Köszönöm!');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `brit_post`
--
-- használatban(#1932 - Table 'brit.brit_post' doesn't exist in engine)
-- Hiba az adatolvasás közben: (#1932 - Table 'brit.brit_post' doesn't exist in engine)

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `brit_post_2`
--

CREATE TABLE `brit_post_2` (
  `post_id` int(11) NOT NULL,
  `post_title` varchar(300) COLLATE utf8_hungarian_ci NOT NULL,
  `post_title_alias` varchar(300) COLLATE utf8_hungarian_ci NOT NULL,
  `post_create_date` datetime NOT NULL,
  `post_modify_date` datetime NOT NULL,
  `post_author` int(11) NOT NULL,
  `post_modify_author` int(11) NOT NULL,
  `post_keywords` text COLLATE utf8_hungarian_ci NOT NULL,
  `post_content` mediumtext COLLATE utf8_hungarian_ci NOT NULL,
  `post_image` varchar(500) COLLATE utf8_hungarian_ci NOT NULL,
  `post_visible` float NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `brit_post_2`
--

INSERT INTO `brit_post_2` (`post_id`, `post_title`, `post_title_alias`, `post_create_date`, `post_modify_date`, `post_author`, `post_modify_author`, `post_keywords`, `post_content`, `post_image`, `post_visible`) VALUES
(1, 'Teszt alpha', 'Teszt_alpha', '2017-09-12 19:09:25', '2017-09-13 07:09:00', 1, 0, '0', '0', 'img/news_uploaded/Teszt_alpha.jpg', 1),
(2, 'Nem forgathattak leszbikusokról a katolikus iskolában', 'Nem_forgathattak_leszbikusokrol_a_katolikus_iskolaban', '2017-09-13 07:09:54', '2017-10-07 09:10:01', 1, 0, 'teszt', '<p>TArtalom.</p>', 'img/news_uploaded/Nem_forgathattak_leszbikusokrol_a_katolikus_iskolaban.jpg', 1),
(3, 'Lányok maszturbálnak', 'Lanyok_maszturbalnak', '2017-09-17 07:09:01', '2017-11-03 16:11:15', 1, 0, 'lányok maszturbálás önkielégítés élvezet', '<p>Lányok maszturbálnak. valami</p>', 'img/news_uploaded/Lanyok_maszturbalnak.jpg', 1),
(5, 'Libidóka', 'Libidoka', '2017-09-17 08:09:38', '2017-09-17 08:09:07', 1, 0, 'lib', '<p>lib-lib</p>', 'img/news_uploaded/Libidoka.jpg', 1),
(6, 'Leszbikus csók flashmob az ELTE-n ', 'Leszbikus_csok_flashmob_az_ELTE-n_', '2017-09-17 08:09:26', '2017-09-22 12:09:21', 1, 0, 'leszbikus csók lányok', '<h3>Lányokat és transzlányokat hív csókolózni a Labrisz Egyesület csütörtök este 8 órára ELTE BTK területén lévő Könyvtár Klubba, a Múzeum körútra. A csók-flashmobbal szeretnének tiltakozni az ellen, hogy nemrég a klubban egy pultos lány a beszámolók szerint „visszataszító hangnemben” kiabált rá két lányra, akik megcsókolták egymást.</h3>\r\n<p> </p>\r\n<div class="article-text">\r\n<p><a href="https://www.facebook.com/LabriszEgyesulet/photos/a.155674114001.139735.110492499001/10154081917854002/?type=3" target="_blank" rel="noopener">A Labrisz Egyesület Facebook-oldalán azt írják</a>: május 11-én az ELTE BTK területén lévő Könyvtár Klubba beült egy ELTE-sekből és nem ELTE-sekből álló baráti csoport. A társasághoz tartozó lánypár tagjai megcsókolták egymást. Egyikük beszámolója szerint "a pultos lány körülbelül 20 méterről ránk ordított, visszataszító hangnemben, hogy: ''lányok, ez nem ide tartozik''. Ezek után teljesen megsemmisültünk, ennyire megalázva még sosem éreztem magam."</p>\r\n<p>A baráti társaság később konzultált a rendőrséggel, majd visszamentek, de ahogy a megaláztatást elszenvedő pár egyik tagja írja: az ELTE Trefort-kertjéhez tartozó intézmény üzletvezetője "arra hivatkozott, hogy joguk van ránk szólni a viselkedésünk miatt" - írja a <a href="http://hvg.hu/itthon/20160525_leszbikus_csok_flashmobot_szerveznek_az_elte_konyvtar_klubba" target="_blank" rel="noopener">hvg.hu</a>.</p>\r\n<p>A Labrisz Egyesület erre válaszul szervez csütörtökön 20 órára leszbikus csók-flashmobot a Könyvtár Klubba. „Gyere el, és csókold meg azt, akit szeretnél, akit jogod van bárhol és bármikor megcsókolni, hisz a ti boldogságotok ugyanolyan értékes, mint bárki másé” – olvasható a felhívásukban.</p>\r\n</div>', 'img/news_uploaded/Leszbikus_csok_flashmob_az_ELTE-n_.jpg', 1);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `brit_rank`
--

CREATE TABLE `brit_rank` (
  `rank_id` int(11) NOT NULL,
  `rank_type` int(11) NOT NULL,
  `rank_name` char(100) COLLATE utf8_hungarian_ci NOT NULL,
  `rank_value` char(100) COLLATE utf8_hungarian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `brit_rank`
--

INSERT INTO `brit_rank` (`rank_id`, `rank_type`, `rank_name`, `rank_value`) VALUES
(1, 0, 'tag', 'nyul'),
(2, 1, 'admin', 'admin'),
(3, 2, 'moderator', 'moderator'),
(4, 3, 'tulajdonos', 'tulajdonos');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `brit_user`
--

CREATE TABLE `brit_user` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(32) COLLATE utf8_hungarian_ci NOT NULL,
  `user_password` varchar(128) COLLATE utf8_hungarian_ci NOT NULL,
  `user_firstname` varchar(32) COLLATE utf8_hungarian_ci NOT NULL,
  `user_lastname` varchar(32) COLLATE utf8_hungarian_ci NOT NULL,
  `user_email` varchar(1024) COLLATE utf8_hungarian_ci NOT NULL,
  `user_email_code` varchar(64) COLLATE utf8_hungarian_ci NOT NULL,
  `user_active` int(11) NOT NULL DEFAULT '0',
  `user_password_recover` int(11) NOT NULL DEFAULT '0',
  `user_type` int(1) NOT NULL DEFAULT '0',
  `user_admin_password` varchar(64) COLLATE utf8_hungarian_ci NOT NULL,
  `user_allow_email` int(11) NOT NULL DEFAULT '1',
  `user_profile` varchar(300) COLLATE utf8_hungarian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `brit_user`
--

INSERT INTO `brit_user` (`user_id`, `user_name`, `user_password`, `user_firstname`, `user_lastname`, `user_email`, `user_email_code`, `user_active`, `user_password_recover`, `user_type`, `user_admin_password`, `user_allow_email`, `user_profile`) VALUES
(1, 'relierf', '37643e626fb594b41cf5c86683523cbb2fdb0ddc', 'Béla', 'Freiler', 'ref@freemail.hu', '6ce2939f9010bd9c7faab9da811f92de8571f3a4', 1, 0, 1, '25c45a6e643ab5429078c3cc26d30f6f04973afe', 0, ''),
(2, 'corinne', 'a98fa760d63cf7120051c8b05d0b8c3f75cf1d5a', 'Eva', 'Antonya', 'corinne.reff@freemail.hu', '2dcf9ea9f2539e204249944e5b5a59811b934583', 1, 1, 0, '', 1, ''),
(13, 'tessa', 'c74cbd47699f3274bebe5403fc89170a4e914508', 'Tessa', 'Fowler', 'cica.reff@freemail.hu', '37648cc967981c365aea18e4d59a3762ff5b4b43', 1, 0, 1, '76a0052379b2a24b0a5206669e1b8facad59d958', 1, ''),
(14, 'sani', 'a71e8ad1c2aa785fd9e1175ccc78561a9c8d2caf', 'Sancho', 'Panza', 'spancho.reff@freemail.hu', '10e344289895fd815550e564d9eaa8d3be20169a', 1, 0, 0, '', 1, ''),
(15, 'gutter', '7386efac2e358a0b7e6fcedb3c5cc4e4579c045c', 'David', '', 'gutter.reff@freemail.hu', '498f0cb470305fdc0916f6267b4836a193e57a04', 1, 0, 0, '', 1, ''),
(16, 'ref', '7f39a3cf4a32a5a2a23c4244014b18b0f5f1a284', 'Béla', 'Freiler', 'ref68.tablet@gmail.com', '9b11e4e841c90af1f07e9ad1e720d787bc11fa83', 1, 0, 0, '', 1, '');

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `brit_articles_2`
--
ALTER TABLE `brit_articles_2`
  ADD PRIMARY KEY (`cikk_id`);

--
-- A tábla indexei `brit_menu`
--
ALTER TABLE `brit_menu`
  ADD PRIMARY KEY (`menu_id`);

--
-- A tábla indexei `brit_news_comment`
--
ALTER TABLE `brit_news_comment`
  ADD PRIMARY KEY (`nwc_id`);

--
-- A tábla indexei `brit_post_2`
--
ALTER TABLE `brit_post_2`
  ADD PRIMARY KEY (`post_id`);

--
-- A tábla indexei `brit_rank`
--
ALTER TABLE `brit_rank`
  ADD PRIMARY KEY (`rank_id`);

--
-- A tábla indexei `brit_user`
--
ALTER TABLE `brit_user`
  ADD PRIMARY KEY (`user_id`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `brit_articles_2`
--
ALTER TABLE `brit_articles_2`
  MODIFY `cikk_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT a táblához `brit_menu`
--
ALTER TABLE `brit_menu`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
--
-- AUTO_INCREMENT a táblához `brit_news_comment`
--
ALTER TABLE `brit_news_comment`
  MODIFY `nwc_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT a táblához `brit_post_2`
--
ALTER TABLE `brit_post_2`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT a táblához `brit_rank`
--
ALTER TABLE `brit_rank`
  MODIFY `rank_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT a táblához `brit_user`
--
ALTER TABLE `brit_user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
