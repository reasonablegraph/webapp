<?php
class ScorpControler extends BaseController {



	public function researchers_report() {
		Log::info('Researchers Report');

		ArcApp::auth_check();

		$dbh = dbconnect();


// 		$SQL="WITH RESEARCHER AS (
// 		SELECT
// 		i.item_id,
// 		i.label,
// 		v.text_value AS login_name
// 		FROM dsd.item2 i
// 		JOIN dsd.metadatavalue2 v ON (i.item_id = v.item_id)
// 		WHERE v.element = 'ea:loginUser:' AND i.status = 'finish'
// 				), REPORT1 AS (
// 				SELECT  r.label as researcher, r.item_id as researcher_id, count (*) as c1
// 				FROM dsd.metadatavalue2 m
// 				--JOIN dsd.item2 i ON (m.item_id = i.item_id)
// 				JOIN RESEARCHER r ON (m.ref_item = r.item_id AND m.element = 'ea:lemma:author')
// 				GROUP BY 1,2
// 				), REPORT2 AS (
// 				SELECT  r.label as researcher, r.item_id as researcher_id, count (*) as c2
// 				FROM dsd.item2 i
// 				JOIN RESEARCHER r ON (i.user_create = r.login_name AND flags @> ARRAY['IS:lemma-person'])
// 				GROUP BY 1,2
// 				) SELECT r0.item_id, r0.label, r1.c1 as lemma_count, r2.c2 as bio_count, coalesce(r1.c1,0)+coalesce(r2.c2,0) as total , 5*coalesce(r1.c1,0)+coalesce(r2.c2,0) as score
// 				FROM RESEARCHER r0
// 				LEFT JOIN REPORT1 r1 ON (r0.item_id = r1.researcher_id)
// 				LEFT JOIN REPORT2 r2 ON (r0.item_id = r2.researcher_id)
// 				ORDER BY r1.researcher";

		$SQL="WITH

				RESEARCHER AS (
				SELECT
				i.item_id,
				i.label,
				v.text_value AS login_name
				FROM dsd.item2 i
				JOIN dsd.metadatavalue2 v ON (i.item_id = v.item_id)
				WHERE v.element = 'ea:loginUser:' AND i.status = 'finish'
				),

				REPORT1 AS (
				SELECT  r.label as researcher, r.item_id as researcher_id, count (*) as c1
				FROM dsd.metadatavalue2 m
				JOIN dsd.item2 i ON (m.item_id = i.item_id)
				JOIN RESEARCHER r ON (m.ref_item = r.item_id AND m.element = 'ea:lemma:author')
				WHERE flags @> ARRAY['IS:lemma-book']
				GROUP BY 1,2
				),

				REPORT2 AS (
				SELECT  r.label as researcher, r.item_id as researcher_id, count (*) as c2
				FROM dsd.metadatavalue2 m
				JOIN dsd.item2 i ON (m.item_id = i.item_id)
				JOIN RESEARCHER r ON (m.ref_item = r.item_id AND m.element = 'ea:lemma:author')
				WHERE flags @> ARRAY['IS:lemma-other']
				GROUP BY 1,2
				),

				REPORT3 AS (
				SELECT  r.label as researcher, r.item_id as researcher_id, count (*) as c3
				FROM dsd.item2 i
				JOIN RESEARCHER r ON (i.user_create = r.login_name AND flags @> ARRAY['IS:lemma-person'])
				GROUP BY 1,2
				)

				SELECT r0.item_id, r0.label, r1.c1 as lemma_book_count, r2.c2 as lemma_other_count, r3.c3 as bio_count, coalesce(r1.c1,0)+coalesce(r2.c2,0)+coalesce(r3.c3,0) as total , 5*coalesce(r1.c1,0)+coalesce(r2.c2,0)+coalesce(r3.c3,0) as score
				FROM RESEARCHER r0
				LEFT JOIN REPORT1 r1 ON (r0.item_id = r1.researcher_id)
				LEFT JOIN REPORT2 r2 ON (r0.item_id = r2.researcher_id)
				LEFT JOIN REPORT3 r3 ON (r0.item_id = r3.researcher_id)
				ORDER BY r1.researcher";

		$stmt1 = $dbh->prepare($SQL);
		$stmt1->execute();


		$data['results'] = $stmt1->fetchAll();
		return $this->show($data);

	}


}



