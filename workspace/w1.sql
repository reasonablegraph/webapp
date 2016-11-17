

SELECT item_id, element,ref_item, text_value,inferred FROM dsd.metadatavalue2
						WHERE obj_class in ('manifestation','actor')
						AND link is null
						AND (element in ('dc:title:','ea:title:specific','ea:obj-type:','ea:status:') OR ref_item is not null)
						AND (
							item_id IN (SELECT ref_item FROM dsd.metadatavalue2 WHERE item_id = 103 AND ref_item is not null)
						)


:do
id1 := 103;
			FOR r1 IN 
				SELECT distinct * FROM (
					(
						SELECT metadata_value_id,item_id,element,ref_item,text_value,text_lang,link,lid,inferred FROM dsd.metadatavalue2
						WHERE obj_class in ('manifestation','actor')
						AND link is null
						AND (element in ('dc:title:', 'ea:title:specific', 'ea:obj-type:', 'ea:status:') OR ref_item is not null)
						AND (
							item_id = id1
							OR item_id in (SELECT item_id FROM dsd.metadatavalue2 WHERE ref_item  = id1)
							)
					) UNION (
						SELECT metadata_value_id,item_id,element,ref_item,text_value,text_lang,link,lid,inferred FROM dsd.metadatavalue2
						WHERE obj_class in ('manifestation','actor')
						AND link is null
						AND (element in ('dc:title:', 'ea:title:specific', 'ea:obj-type:', 'ea:status:') OR ref_item is not null)
						AND (
							item_id IN (SELECT ref_item FROM dsd.metadatavalue2 WHERE item_id in (id1) AND ref_item is not null)
						)
					)
				) as foo ORDER BY item_id
			LOOP
				RAISE NOTICe '%',r1;
			END LOOP;

:done;





				SELECT distinct * FROM (
					(
						SELECT metadata_value_id,item_id,element,ref_item,text_value,text_lang,link,lid,inferred FROM dsd.metadatavalue2
						WHERE obj_class in ('manifestation','actor')
						AND link is null
						AND (element in ('dc:title:', 'ea:title:specific', 'ea:obj-type:', 'ea:status:') OR ref_item is not null)
						AND (
							item_id = 103
							OR item_id in (SELECT item_id FROM dsd.metadatavalue2 WHERE ref_item  = 103)
							)
					) UNION (
						SELECT metadata_value_id,item_id,element,ref_item,text_value,text_lang,link,lid,inferred FROM dsd.metadatavalue2
						WHERE obj_class in ('manifestation','actor')
						AND link is null
						AND (element in ('dc:title:', 'ea:title:specific', 'ea:obj-type:', 'ea:status:') OR ref_item is not null)
						AND (
							item_id IN (SELECT ref_item FROM dsd.metadatavalue2 WHERE item_id in (101) AND ref_item is not null)
						)
					)
				) as foo ORDER BY item_id

