-- ALPHA CAPITALIS client content review — live database update.
-- Generated from the verified local CMS state on 2026-08-21.
-- MySQL/MariaDB; safe to import repeatedly through phpMyAdmin.
--
-- IMPORTANT:
-- 1. Deploy the matching application code before importing.
-- 2. This SQL updates CMS text, counts and only the requested editorial images:
--    the Career hero/gallery and the Audit image. It does not change team-member
--    activation, ordering, contact details, photos, or other team settings.
-- 3. Within the team content, it changes only Danijel's displayed credential and
--    Ana Mandić's Croatian biography, when those members already exist.
-- 4. This script does not mark Laravel migrations as applied.

SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;
START TRANSACTION;

-- Homepage slogan.
UPDATE content_block_translations AS translation
INNER JOIN content_blocks AS block
    ON block.id = translation.content_block_id
SET
    translation.title = CASE
        WHEN translation.locale = 'hr' THEN 'Vaš kompas kroz svijet financija'
        ELSE 'Your compass through the world of finance'
    END,
    translation.subtitle = CASE
        WHEN translation.locale = 'hr' THEN 'Računovodstvo, revizija i savjetovanje — sve na jednom mjestu.'
        ELSE 'Accounting, audit and advisory — all in one place.'
    END,
    translation.updated_at = NOW()
WHERE block.code = 'home-alpha-hero'
  AND translation.locale IN ('hr', 'en');

-- Homepage client and expert counts.
UPDATE content_block_translations AS translation
INNER JOIN content_blocks AS block
    ON block.id = translation.content_block_id
SET
    translation.payload = JSON_SET(
        COALESCE(translation.payload, JSON_OBJECT()),
        '$.stats[1].value', '700',
        '$.stats[1].suffix', '',
        '$.stats[2].value', '75',
        '$.stats[2].suffix', ''
    ),
    translation.updated_at = NOW()
WHERE block.code = 'home-alpha-stats'
  AND translation.locale IN ('hr', 'en');

-- About page: exact verified Croatian and English CMS payloads.
SET @ac_about_page_id := (
    SELECT id
    FROM content_info_pages
    WHERE code = 'about-us' OR layout = 'about'
    ORDER BY CASE WHEN code = 'about-us' THEN 0 ELSE 1 END, id
    LIMIT 1
);

UPDATE content_info_page_translations
SET
    payload = JSON_SET(
        COALESCE(payload, JSON_OBJECT()),
        '$.about_page',
        JSON_EXTRACT(CONVERT(FROM_BASE64(
        CONCAT(
            'eyJ3aHkiOnsicXVvdGUiOiJLdmFsaXRldG5hIHN0cnXEjW5hIHBvZHLFoWthIG5pamUgc2FtbyBvZGdvdm9yIGthZGEgc2UgcHJvYmxlbSB2ZcSHIHBvamF2',
            'aS4iLCJ0aXRsZSI6IlV6IHZhcyBwcmlqZSwgdGlqZWtvbSBpIG5ha29uIHN2YWtlIHZhxb5uZSBvZGx1a2UiLCJraWNrZXIiOiJaYcWhdG8gcG9zdG9qaW1v',
            'IiwiYm9keV9odG1sIjoiPHA+TmHFoSBqZSBjaWxqIHBvbW/Eh2kga2xpamVudGltYSBkYSBwcmV2bGFkYWp1IHByZXByZWtlIHByaWplIG5lZ28gxaF0byBw',
            'b3N0YW51IG96YmlsamFuIHByb2JsZW0sIGRhIGl6YmplZ251IG5hanRlxb5lIHNjZW5hcmlqZSBrYWRhIGplIHRvIG1vZ3XEh2UgaSBkYSwgYWtvIHNlIGlw',
            'YWsgbmHEkXUgdSB6YWh0amV2bm9qIHNpdHVhY2lqaSwgemFqZWRubyBwcm9uYcSRZW1vIHB1dCBkYWxqZS48L3A+PHA+VmplcnVqZW1vIGRhIGt2YWxpdGV0',
            'bmEgc3RydcSNbmEgcG9kcsWha2Egem5hxI1pIGltYXRpIHByYXZlIGxqdWRlIHV6IHNlYmUgcHJpamUsIHRpamVrb20gaSBuYWtvbiBzdmFrZSB2YcW+bmUg',
            'cG9zbG92bmUgb2RsdWtlLjwvcD4iLCJwYXJhZ3JhcGhzIjpbIk5hxaEgamUgY2lsaiBwb21vxIdpIGtsaWplbnRpbWEgZGEgcHJldmxhZGFqdSBwcmVwcmVr',
            'ZSBwcmlqZSBuZWdvIMWhdG8gcG9zdGFudSBvemJpbGphbiBwcm9ibGVtLCBkYSBpemJqZWdudSBuYWp0ZcW+ZSBzY2VuYXJpamUga2FkYSBqZSB0byBtb2d1',
            'xIdlIGkgZGEsIGFrbyBzZSBpcGFrIG5hxJF1IHUgemFodGpldm5vaiBzaXR1YWNpamksIHphamVkbm8gcHJvbmHEkWVtbyBwdXQgZGFsamUuIiwiVmplcnVq',
            'ZW1vIGRhIGt2YWxpdGV0bmEgc3RydcSNbmEgcG9kcsWha2Egem5hxI1pIGltYXRpIHByYXZlIGxqdWRlIHV6IHNlYmUgcHJpamUsIHRpamVrb20gaSBuYWtv',
            'biBzdmFrZSB2YcW+bmUgcG9zbG92bmUgb2RsdWtlLiJdfSwiaGVybyI6eyJsZWFkIjoiVmnFoWUgb2Qgc3RydcSNbmUgcG9kcsWha2UuIFBhcnRuZXIga3Jv',
            'eiBzdmFrdSBmYXp1IHBvc2xvdmFuamEuIiwidGl0bGUiOiJOYcWhYSBwcmnEjWEiLCJleWVicm93IjoiTyBuYW1hIiwiaW1hZ2VfYWx0IjoiIiwic3RhdF9s',
            'YWJlbCI6ImtsaWplbmF0YSBrb2ppbWEgc3Zha29kbmV2bm8gcHJ1xb5hbW8gcG9kcsWha3UiLCJzdGF0X3ZhbHVlIjoiNzAwIn0sInRlYW0iOnsiYm9keSI6',
            'IlphdG8gdSBBTFBIQSBDQVBJVEFMSVNVIHBvdmV6dWplbW8gc3RydcSNbmpha2UgaXogcmHEjXVub3ZvZHN0dmEsIHJldml6aWplLCBwb3JlemEsIGZpbmFu',
            'Y2lqYSBpIHBvc2xvdm5vZyBzYXZqZXRvdmFuamEuIEthZGEgamUgcG90cmVibm8sIHphIGlzdGltIGl6YXpvdm9tIG9rdXBsamFtbyByYXpsacSNaXRlIHBl',
            'cnNwZWt0aXZlIGkgem5hbmphIGtha28gYmkga2xpamVudCBkb2JpbyBwb3ZlemFuIHRpbSBrb2ppIHJhenVtaWplIMWhaXJpIGtvbnRla3N0IG5qZWdvdmEg',
            'cG9zbG92YW5qYSBpIG1vxb5lIHBydcW+aXRpIHBvZHLFoWt1IGtyb3ogcmF6bGnEjWl0ZSBmYXplIHJhenZvamEuIiwiaW50cm8iOiJSYXpsacSNaXRpIGl6',
            'YXpvdmkgemFodGlqZXZhanUgcmF6bGnEjWl0YSB6bmFuamEuIFN2YWtvIHBvc2xvdmFuamUgamUgZHJ1Z2HEjWlqZSwgYSBvbm8gxaF0byBqZSBqZWRub2og',
            'dHZydGtpIHBvdHJlYm5vIHUgZmF6aSBvc25pdmFuamEgbmlqZSBpc3RvIMWhdG8gam9qIGplIHBvdHJlYm5vIHRpamVrb20gcmFzdGEsIMWhaXJlbmphLCBy',
            'ZW9yZ2FuaXphY2lqZSBpbGkgcHJpamVub3NhIHBvc2xvdmFuamEgbmEgbm92dSBnZW5lcmFjaWp1LiIsImxhYmVsIjoiTmHFoSB0aW0iLCJzdGF0cyI6W3si',
            'bGFiZWwiOiJzdHJ1xI1uamFrYSIsInZhbHVlIjoiNzUifSx7ImxhYmVsIjoixI1sYW5vdmEgdXByYXZlIiwidmFsdWUiOiI5In0seyJsYWJlbCI6ImtsaWpl',
            'bmF0YSIsInZhbHVlIjoiNzAwIn0seyJsYWJlbCI6InVyZWRhIHUgWmFncmVidSwgVmlua292Y2ltYSBpIFJpamVjaSIsInZhbHVlIjoiMyJ9XSwidGl0bGUi',
            'OiJUaW0gc3RydcSNbmpha2EgbmEgamVkbm9tIG1qZXN0dSIsImtpY2tlciI6IlRJTSIsImJvZHlfaHRtbCI6IjxwPlJhemxpxI1pdGkgaXphem92aSB6YWh0',
            'aWpldmFqdSByYXpsacSNaXRhIHpuYW5qYS4gU3Zha28gcG9zbG92YW5qZSBqZSBkcnVnYcSNaWplLCBhIG9ubyDFoXRvIGplIGplZG5vaiB0dnJ0a2kgcG90',
            'cmVibm8gdSBmYXppIG9zbml2YW5qYSBuaWplIGlzdG8gxaF0byBqb2ogamUgcG90cmVibm8gdGlqZWtvbSByYXN0YSwgxaFpcmVuamEsIHJlb3JnYW5pemFj',
            'aWplIGlsaSBwcmlqZW5vc2EgcG9zbG92YW5qYSBuYSBub3Z1IGdlbmVyYWNpanUuPC9wPjxwPlphdG8gdSBBTFBIQSBDQVBJVEFMSVNVIHBvdmV6dWplbW8g',
            'c3RydcSNbmpha2UgaXogcmHEjXVub3ZvZHN0dmEsIHJldml6aWplLCBwb3JlemEsIGZpbmFuY2lqYSBpIHBvc2xvdm5vZyBzYXZqZXRvdmFuamEuIEthZGEg',
            'amUgcG90cmVibm8sIHphIGlzdGltIGl6YXpvdm9tIG9rdXBsamFtbyByYXpsacSNaXRlIHBlcnNwZWt0aXZlIGkgem5hbmphIGtha28gYmkga2xpamVudCBk',
            'b2JpbyBwb3ZlemFuIHRpbSBrb2ppIHJhenVtaWplIMWhaXJpIGtvbnRla3N0IG5qZWdvdmEgcG9zbG92YW5qYSBpIG1vxb5lIHBydcW+aXRpIHBvZHLFoWt1',
            'IGtyb3ogcmF6bGnEjWl0ZSBmYXplIHJhenZvamEuPC9wPiIsImJ1dHRvbl9sYWJlbCI6IlVwb3puYWogY2lqZWxpIHRpbSJ9LCJzdG9yeSI6eyJ0aXRsZSI6',
            'IlZpxaFlIG9kIHN0cnXEjW5lIHBvZHLFoWtlLiBQYXJ0bmVyIGtyb3ogc3Zha3UgZmF6dSBwb3Nsb3ZhbmphLiIsImtpY2tlciI6Ik5hxaFhIHByacSNYSIs',
            'ImJvZHlfaHRtbCI6IjxwPkFMUEhBIENBUElUQUxJUyBva3VwbGphIHN0cnXEjW5qYWtlIGl6IHBvZHJ1xI1qYSByYcSNdW5vdm9kc3R2YSwgcmV2aXppamUg',
            'aSBzYXZqZXRvdmFuamEgcyBqZWRub20gemFqZWRuacSNa29tIGlkZWpvbSDigJMgcG9tb8SHaSBwb2R1emV0bmljaW1hIGRhIHNpZ3VybmlqZSBwcm9sYXpl',
            'IGtyb3ogc3ZlIGZhemUgcG9zbG92YW5qYS48L3A+XG48cD48c3Ryb25nPkplciBwb3Nsb3ZhbmplIG5pamUgcmF2bmEgbGluaWphLjwvc3Ryb25nPjwvcD5c',
            'bjxwPlR2cnRrZSByYXN0dSwgbWlqZW5qYWp1IHNlLCB1bGF6ZSB1IG5vdmEgdHLFvmnFoXRhLCB6YXBvxaFsamF2YWp1LCB1bGHFvnUsIHByb2xhemUga3Jv',
            'eiBpemF6b3ZlIGkgZG9ub3NlIG9kbHVrZSBrb2plIG1vZ3Ugb2RyZWRpdGkgbmppaG92IHNsamVkZcSHaSBrb3Jhay4gUG9uZWthZCBqZSBwb3RyZWJubyBw',
            'cm9uYcSHaSBwcmlsaWt1IHphIHJhc3QuIFBvbmVrYWQgemHFoXRpdGl0aSBvbm8gxaF0byBzZSBnb2RpbmFtYSBncmFkaWxvLiBBIHBvbmVrYWQgcHJvbmHE',
            'h2kgcmplxaFlbmplIGthZGEgc3R2YXJpIG5lIGlkdSBwcmVtYSBwbGFudS48L3A+XG48cD48c3Ryb25nPlVwcmF2byB6YXRvIHBvc3Rvamltby48L3N0cm9u',
            'Zz48L3A+XG48cD7FvWVsaW1vIGJpdGkgdXogcG9kdXpldG5pa2Uga2FkYSBkb25vc2UgdmHFvm5lIG9kbHVrZSwga2FkYSBwcmVsYXplIGl6IGplZG5lIGZh',
            'emUgcG9zbG92YW5qYSB1IGRydWd1IGkga2FkYSBzZSBzdXNyZcSHdSBzIHByb2JsZW1pbWEgemEga29qZSBuaWplIGRvdm9sam5vIGplZG5vIG1pxaFsamVu',
            'amUgaWxpIGplZG5vIHBvZHJ1xI1qZSBzdHJ1xI1ub3N0aS48L3A+XG48cD48c3Ryb25nPkplciBqZWRuYSBvc29iYSBuZSBtb8W+ZSB6bmF0aSBpIHJpamXF',
            'oWl0aSBzdmUuPC9zdHJvbmc+PC9wPlxuPHA+QWxpIDxzdHJvbmc+c25hxb5hbiBtdWx0aWRpc2NpcGxpbmFybmkgdGltPC9zdHJvbmc+IG1vxb5lIHNhZ2xl',
            'ZGF0aSBwb3Nsb3ZhbmplIGl6IHZpxaFlIHBlcnNwZWt0aXZhLCBwb3ZlemF0aSByYXpsacSNaXRhIHpuYW5qYSBpIHBvbW/Eh2kgcHJvbmHEh2kgY2plbG92',
            'aXRpamUgcmplxaFlbmplLjwvcD5cbjxwPlphdG8gbmHFoWkga2xpamVudGkgdXogc2ViZSBuZW1hanUgc2FtbyBqZWRub2cgc2F2amV0bmlrYS4gPHN0cm9u',
            'Zz5JbWFqdSB0aW0gc3RydcSNbmpha2Ega29qaSByYXp1bWlqZSByYXpsacSNaXRlIGRpamVsb3ZlIHBvc2xvdmFuamE8L3N0cm9uZz4gaSBrb2ppIG1vxb5l',
            'IHBydcW+aXRpIHBvZHLFoWt1IG9uZGEga2FkYSBqZSBvbmEgbmFqcG90cmVibmlqYSwgb2Qgc3Zha29kbmV2bm9nIHJhxI11bm92b2RzdHZhIGkgcG9yZXpu',
            'aWggcGl0YW5qYSBkbyBmaW5hbmNpanNraWggb2RsdWthLCByZXZpemlqZSwgcmFzdGEsIHByb21qZW5hIGkgc2xvxb5lbmlqaWggcG9zbG92bmloIGl6YXpv',
            'dmEuPC9wPiIsInBhcmFncmFwaHMiOlsiQUxQSEEgQ0FQSVRBTElTIG9rdXBsamEgc3RydcSNbmpha2UgaXogcG9kcnXEjWphIHJhxI11bm92b2RzdHZhLCBy',
            'ZXZpemlqZSBpIHNhdmpldG92YW5qYSBzIGplZG5vbSB6YWplZG5pxI1rb20gaWRlam9tIOKAkyBwb21vxIdpIHBvZHV6ZXRuaWNpbWEgZGEgc2lndXJuaWpl',
            'IHByb2xhemUga3JveiBzdmUgZmF6ZSBwb3Nsb3ZhbmphLiIsIkplciBwb3Nsb3ZhbmplIG5pamUgcmF2bmEgbGluaWphLiIsIlR2cnRrZSByYXN0dSwgbWlq',
            'ZW5qYWp1IHNlLCB1bGF6ZSB1IG5vdmEgdHLFvmnFoXRhLCB6YXBvxaFsamF2YWp1LCB1bGHFvnUsIHByb2xhemUga3JveiBpemF6b3ZlIGkgZG9ub3NlIG9k',
            'bHVrZSBrb2plIG1vZ3Ugb2RyZWRpdGkgbmppaG92IHNsamVkZcSHaSBrb3Jhay4gUG9uZWthZCBqZSBwb3RyZWJubyBwcm9uYcSHaSBwcmlsaWt1IHphIHJh',
            'c3QuIFBvbmVrYWQgemHFoXRpdGl0aSBvbm8gxaF0byBzZSBnb2RpbmFtYSBncmFkaWxvLiBBIHBvbmVrYWQgcHJvbmHEh2kgcmplxaFlbmplIGthZGEgc3R2',
            'YXJpIG5lIGlkdSBwcmVtYSBwbGFudS4iLCJVcHJhdm8gemF0byBwb3N0b2ppbW8uIiwixb1lbGltbyBiaXRpIHV6IHBvZHV6ZXRuaWtlIGthZGEgZG9ub3Nl',
            'IHZhxb5uZSBvZGx1a2UsIGthZGEgcHJlbGF6ZSBpeiBqZWRuZSBmYXplIHBvc2xvdmFuamEgdSBkcnVndSBpIGthZGEgc2Ugc3VzcmXEh3UgcyBwcm9ibGVt',
            'aW1hIHphIGtvamUgbmlqZSBkb3ZvbGpubyBqZWRubyBtacWhbGplbmplIGlsaSBqZWRubyBwb2RydcSNamUgc3RydcSNbm9zdGkuIiwiSmVyIGplZG5hIG9z',
            'b2JhIG5lIG1vxb5lIHpuYXRpIGkgcmlqZcWhaXRpIHN2ZS4iLCJBbGkgc25hxb5hbiBtdWx0aWRpc2NpcGxpbmFybmkgdGltIG1vxb5lIHNhZ2xlZGF0aSBw',
            'b3Nsb3ZhbmplIGl6IHZpxaFlIHBlcnNwZWt0aXZhLCBwb3ZlemF0aSByYXpsacSNaXRhIHpuYW5qYSBpIHBvbW/Eh2kgcHJvbmHEh2kgY2plbG92aXRpamUg',
            'cmplxaFlbmplLiIsIlphdG8gbmHFoWkga2xpamVudGkgdXogc2ViZSBuZW1hanUgc2FtbyBqZWRub2cgc2F2amV0bmlrYS4gSW1hanUgdGltIHN0cnXEjW5q',
            'YWthIGtvamkgcmF6dW1pamUgcmF6bGnEjWl0ZSBkaWplbG92ZSBwb3Nsb3ZhbmphIGkga29qaSBtb8W+ZSBwcnXFvml0aSBwb2RyxaFrdSBvbmRhIGthZGEg',
            'amUgb25hIG5hanBvdHJlYm5pamEsIG9kIHN2YWtvZG5ldm5vZyByYcSNdW5vdm9kc3R2YSBpIHBvcmV6bmloIHBpdGFuamEgZG8gZmluYW5jaWpza2loIG9k',
            'bHVrYSwgcmV2aXppamUsIHJhc3RhLCBwcm9tamVuYSBpIHNsb8W+ZW5pamloIHBvc2xvdm5paCBpemF6b3ZhLiJdfSwidmFsdWVzIjp7ImludHJvIjoiVSBB',
            'TFBIQSBDQVBJVEFMSVNVIHZyaWplZG5vc3RpIG5pc3Ugc2FtbyByaWplxI1pIC0gb25lIG9kcmXEkXVqdSBrYWtvIHJhem1pxaFsamFtbywga2FrbyByYWRp',
            'bW8gaSBrYWtvIGdyYWRpbW8gb2Rub3NlLiBPbmUgc3UgcHJpc3V0bmUgdSBzdmFrb2RuZXZuaW0gb2RsdWthbWEsIHUgbmHEjWludSBuYSBrb2ppIHN1cmHE',
            'kXVqZW1vIHVudXRhciB0aW1hIGkgdSBvZG5vc3Uga29qaSBncmFkaW1vIHMga2xpamVudGltYS4iLCJpdGVtcyI6W3sibGVhZCI6IlZvbGltbyBsanVkZSBr',
            'b2ppIMW+ZWxlIHXEjWl0aSwgcGl0YXRpLCBpc3RyYcW+aXZhdGkgaSBicnpvIHNlIHJhenZpamF0aS4iLCJ0aXRsZSI6IkxlYXJuIGZhc3QiLCJib2R5X2h0',
            'bWwiOiI8cD5Wb2xpbW8gbGp1ZGUga29qaSDFvmVsZSB1xI1pdGksIHBpdGF0aSwgaXN0cmHFvml2YXRpIGkgYnJ6byBzZSByYXp2aWphdGkuPC9wPjxwPlJh',
            'ZGltbyB1IG9rcnXFvmVuanUga29qZSBzZSBzdGFsbm8gbWlqZW5qYSAtIHRyxb5pxaF0ZSwgemFrb25pLCB0ZWhub2xvZ2lqYSwgcG90cmViZSBrbGlqZW5h',
            'dGEuIFphdG8gdmplcnVqZW1vIGRhIGplIHNwb3NvYm5vc3QgYnJ6b2cgdcSNZW5qYSBqZWRuYSBvZCBuYWp2YcW+bmlqaWggc3R2YXJpIGtvamUgbW/FvmVt',
            'byBpbWF0aSBrYW8gdGltLjwvcD48cD5Lb2QgbmFzIG5pamUgcHJvYmxlbSBuZSB6bmF0aS4gUHJvYmxlbSBqZSBuZSBodGpldGkgbmF1xI1pdGkuPC9wPjxw',
            'PlphdG8gZGlqZWxpbW8gem5hbmplLCB1xI1pbW8gamVkbmkgb2QgZHJ1Z2loLCByYXp2aWphbW8gc2Uga3JveiBwcmFrc3UgaSBuZSDEjWVrYW1vIHNhdnLF',
            'oWVuaSB0cmVudXRhayBkYSBwcmV1em1lbW8gb2Rnb3Zvcm5vc3QuIFXEjWltbyBicnpvIGplciDFvmVsaW1vIGJpdGkgYm9samkgLSB6YSBzZWJlLCB6YSB0',
            'aW0gaSB6YSBrbGlqZW50ZS48L3A+IiwicGFyYWdyYXBocyI6WyJSYWRpbW8gdSBva3J1xb5lbmp1IGtvamUgc2Ugc3RhbG5vIG1pamVuamEgLSB0csW+acWh',
            'dGUsIHpha29uaSwgdGVobm9sb2dpamEsIHBvdHJlYmUga2xpamVuYXRhLiBaYXRvIHZqZXJ1amVtbyBkYSBqZSBzcG9zb2Jub3N0IGJyem9nIHXEjWVuamEg',
            'amVkbmEgb2QgbmFqdmHFvm5pamloIHN0dmFyaSBrb2plIG1vxb5lbW8gaW1hdGkga2FvIHRpbS4iLCJLb2QgbmFzIG5pamUgcHJvYmxlbSBuZSB6bmF0aS4g',
            'UHJvYmxlbSBqZSBuZSBodGpldGkgbmF1xI1pdGkuIiwiWmF0byBkaWplbGltbyB6bmFuamUsIHXEjWltbyBqZWRuaSBvZCBkcnVnaWgsIHJhenZpamFtbyBz',
            'ZSBrcm96IHByYWtzdSBpIG5lIMSNZWthbW8gc2F2csWhZW5pIHRyZW51dGFrIGRhIHByZXV6bWVtbyBvZGdvdm9ybm9zdC4gVcSNaW1vIGJyem8gamVyIMW+',
            'ZWxpbW8gYml0aSBib2xqaSAtIHphIHNlYmUsIHphIHRpbSBpIHphIGtsaWplbnRlLiJdfSx7ImxlYWQiOiJOZSB2amVydWplbW8gdSBrdWx0dXJ1IFwib3N0',
            'YW5pIGR1xb5lIHBhIMSHZSBpemdsZWRhdGkgZGEgcHVubyByYWRpxaFcIi4iLCJ0aXRsZSI6Ildvcmsgc21hcnQsIG5vdCBoYXJkIiwiYm9keV9odG1sIjoi',
            'PHA+TmUgdmplcnVqZW1vIHUga3VsdHVydSAmcXVvdDtvc3RhbmkgZHXFvmUgcGEgxIdlIGl6Z2xlZGF0aSBkYSBwdW5vIHJhZGnFoSZxdW90Oy48L3A+PHA+',
            'VmplcnVqZW1vIHUgcGFtZXRhbiByYWQuIFRvIHpuYcSNaSBkYSByYXptacWhbGphbW8gdW5hcHJpamVkLCBwb3N0YXZsamFtbyBwcmlvcml0ZXRlLCB0cmHF',
            'vmltbyBib2xqYSByamXFoWVuamEgaSBuZSByYWRpbW8gc3R2YXJpIHNhbW8gemF0byDFoXRvIHN1IHNlIHV2aWplayB0YWtvIHJhZGlsZS48L3A+PHA+Vm9s',
            'aW1vIGxqdWRlIGtvamkgcHJlcG96bmFqdSBwcm9ibGVtLCBhbGkgam/FoSB2acWhZSBvbmUga29qaSBwcmVkbGHFvnUgcmplxaFlbmplLiBaYSBuYXMgcHJv',
            'ZHVrdGl2bm9zdCBuaWplIGthb3MsIG5lZ28gZm9rdXMuIE5pamUgdmnFoWUgc2F0aSwgbmVnbyBib2xqaSBuYcSNaW4gcmFkYS48L3A+PHA+xb1lbGltbyBz',
            'dHZhcmF0aSByZXp1bHRhdGUgYmV6IG5lcG90cmVibmUga29tcGxla3Nub3N0aSAtIGt2YWxpdGV0bm8sIG9kZ292b3JubyBpIHMgamFzbmltIGNpbGplbS48',
            'L3A+IiwicGFyYWdyYXBocyI6WyJWamVydWplbW8gdSBwYW1ldGFuIHJhZC4gVG8gem5hxI1pIGRhIHJhem1pxaFsamFtbyB1bmFwcmlqZWQsIHBvc3Rhdmxq',
            'YW1vIHByaW9yaXRldGUsIHRyYcW+aW1vIGJvbGphIHJqZcWhZW5qYSBpIG5lIHJhZGltbyBzdHZhcmkgc2FtbyB6YXRvIMWhdG8gc3Ugc2UgdXZpamVrIHRh',
            'a28gcmFkaWxlLiIsIlZvbGltbyBsanVkZSBrb2ppIHByZXBvem5hanUgcHJvYmxlbSwgYWxpIGpvxaEgdmnFoWUgb25lIGtvamkgcHJlZGxhxb51IHJqZcWh',
            'ZW5qZS4gWmEgbmFzIHByb2R1a3Rpdm5vc3QgbmlqZSBrYW9zLCBuZWdvIGZva3VzLiBOaWplIHZpxaFlIHNhdGksIG5lZ28gYm9samkgbmHEjWluIHJhZGEu',
            'Iiwixb1lbGltbyBzdHZhcmF0aSByZXp1bHRhdGUgYmV6IG5lcG90cmVibmUga29tcGxla3Nub3N0aSAtIGt2YWxpdGV0bm8sIG9kZ292b3JubyBpIHMgamFz',
            'bmltIGNpbGplbS4iXX0seyJsZWFkIjoiTGp1ZGkgc3UgdXZpamVrIHZhxb5uaWppIG9kIHByb2Nlc2EuIiwidGl0bGUiOiJSZWxhdGlvbnNoaXAgb3ZlciB0',
            'cmFuc2FjdGlvbiIsImJvZHlfaHRtbCI6IjxwPkxqdWRpIHN1IHV2aWplayB2YcW+bmlqaSBvZCBwcm9jZXNhLjwvcD48cD5OZSBncmFkaW1vIG9kbm9zZSBr',
            'b2ppIHRyYWp1IGplZGFuIHByb2pla3QgaWxpIGplZGFuIGUtbWFpbC4gR3JhZGltbyBwYXJ0bmVyc3R2YS48L3A+PHA+VG8gdnJpamVkaSB6YSBrbGlqZW50',
            'ZSwgYWxpIGkgemEgdGltLiBWamVydWplbW8gZGEgc2UgcG92amVyZW5qZSBncmFkaSBkb3N0dXBub8WhxId1LCBpc2tyZW5vxaHEh3UsIGt2YWxpdGV0bm9t',
            'IGtvbXVuaWthY2lqb20gaSBzcHJlbW5vxaHEh3UgZGEgYnVkZW1vIHR1IGthZGEgamUgdmHFvm5vLjwvcD48cD5Wb2xpbW8gZHVnb3JvxI1uZSBvZG5vc2Ug',
            'amVyIHZqZXJ1amVtbyBkYSBuYWpib2xqZSBzdHZhcmkgbmFzdGFqdSBrYWRhIHBvc3RvamkgcG92amVyZW5qZSBpIGthZGEgbGp1ZGkgc3R2YXJubyByYXp1',
            'bWlqdSBqZWRuaSBkcnVnZS4gTmEga3JhanUgZGFuYSwgYnJvamtlIGplc3UgdmHFvm5lLCBhbGkgbGp1ZGkgc3UgcmF6bG9nIHphxaF0byBwb3NhbyBpbWEg',
            'c21pc2xhLjwvcD4iLCJwYXJhZ3JhcGhzIjpbIk5lIGdyYWRpbW8gb2Rub3NlIGtvamkgdHJhanUgamVkYW4gcHJvamVrdCBpbGkgamVkYW4gZS1tYWlsLiBH',
            'cmFkaW1vIHBhcnRuZXJzdHZhLiIsIlRvIHZyaWplZGkgemEga2xpamVudGUsIGFsaSBpIHphIHRpbS4gVmplcnVqZW1vIGRhIHNlIHBvdmplcmVuamUgZ3Jh',
            'ZGkgZG9zdHVwbm/FocSHdSwgaXNrcmVub8WhxId1LCBrdmFsaXRldG5vbSBrb211bmlrYWNpam9tIGkgc3ByZW1ub8WhxId1IGRhIGJ1ZGVtbyB0dSBrYWRh',
            'IGplIHZhxb5uby4iLCJWb2xpbW8gZHVnb3JvxI1uZSBvZG5vc2UgamVyIHZqZXJ1amVtbyBkYSBuYWpib2xqZSBzdHZhcmkgbmFzdGFqdSBrYWRhIHBvc3Rv',
            'amkgcG92amVyZW5qZSBpIGthZGEgbGp1ZGkgc3R2YXJubyByYXp1bWlqdSBqZWRuaSBkcnVnZS4gTmEga3JhanUgZGFuYSwgYnJvamtlIGplc3UgdmHFvm5l',
            'LCBhbGkgbGp1ZGkgc3UgcmF6bG9nIHphxaF0byBwb3NhbyBpbWEgc21pc2xhLiJdfV0sImxhYmVsIjoiTmHFoWUgdnJpamVkbm9zdGkiLCJ0aXRsZSI6Ikpl',
            'ZG5vc3Rhdm5pIHByaW5jaXBpIGtvamkgdm9kZSBzdmFraSBkYW4iLCJraWNrZXIiOiJOYcWhZSB2cmlqZWRub3N0aSJ9LCJjdWx0dXJlIjp7InF1b3RlIjoi',
            'VSBBTFBIQSBDQVBJVEFMSVNVIHZqZXJ1amVtbyBkYSBrdmFsaXRldG5vIHBvc2xvdmFuamUgcG/EjWluamUga3ZhbGl0ZXRuaW0gb2Rub3NpbWEuIiwidGl0',
            'bGUiOiJLdmFsaXRldG5vIHBvc2xvdmFuamUgcG/EjWluamUga3ZhbGl0ZXRuaW0gb2Rub3NpbWEiLCJraWNrZXIiOiJOYcWhYSBrdWx0dXJhIiwiYm9keV9o',
            'dG1sIjoiPHA+R3JhZGltbyBrdWx0dXJ1IGtvamEgcG90acSNZSBzdXJhZG5qdSwgcHJvZmVzaW9uYWxuaSByYXp2b2osIG90dm9yZW51IGtvbXVuaWthY2lq',
            'dSBpIG1lxJF1c29ibm8gcG/FoXRvdmFuamUuPC9wPjxwPk5hxaEgdGltIMSNaW5lIGxqdWRpIHJhemxpxI1pdGloIGlza3VzdGF2YSBpIHN0cnXEjW5vc3Rp',
            'IGtvamUgcG92ZXp1amUgemFqZWRuacSNa2kgY2lsaiAtIHBydcW+aXRpIGtsaWplbnRpbWEgbmFqYm9sanUgbW9ndcSHdSBwb2RyxaFrdS48L3A+PHA+UG90',
            'acSNZW1vIGtvbnRpbnVpcmFubyB1xI1lbmplLCByYXptamVudSB6bmFuamEgaSByYXp2b2ogbm92aWggaWRlamEgamVyIHZqZXJ1amVtbyBkYSB1cHJhdm8g',
            'bGp1ZGkgxI1pbmUgbmFqdmXEh3UgcmF6bGlrdS48L3A+PHA+VXogcHJvZmVzaW9uYWxub3N0LCBqZWRuYWtvIG5hbSBqZSB2YcW+bmEgcG96aXRpdm5hIHJh',
            'ZG5hIGF0bW9zZmVyYSwgb3NqZcSHYWogcHJpcGFkbm9zdGkgaSB6YWplZG5pxI1raSByYXN0LjwvcD4iLCJwYXJhZ3JhcGhzIjpbIkdyYWRpbW8ga3VsdHVy',
            'dSBrb2phIHBvdGnEjWUgc3VyYWRuanUsIHByb2Zlc2lvbmFsbmkgcmF6dm9qLCBvdHZvcmVudSBrb211bmlrYWNpanUgaSBtZcSRdXNvYm5vIHBvxaF0b3Zh',
            'bmplLiIsIk5hxaEgdGltIMSNaW5lIGxqdWRpIHJhemxpxI1pdGloIGlza3VzdGF2YSBpIHN0cnXEjW5vc3RpIGtvamUgcG92ZXp1amUgemFqZWRuacSNa2kg',
            'Y2lsaiAtIHBydcW+aXRpIGtsaWplbnRpbWEgbmFqYm9sanUgbW9ndcSHdSBwb2RyxaFrdS4iLCJQb3RpxI1lbW8ga29udGludWlyYW5vIHXEjWVuamUsIHJh',
            'em1qZW51IHpuYW5qYSBpIHJhenZvaiBub3ZpaCBpZGVqYSBqZXIgdmplcnVqZW1vIGRhIHVwcmF2byBsanVkaSDEjWluZSBuYWp2ZcSHdSByYXpsaWt1LiIs',
            'IlV6IHByb2Zlc2lvbmFsbm9zdCwgamVkbmFrbyBuYW0gamUgdmHFvm5hIHBveml0aXZuYSByYWRuYSBhdG1vc2ZlcmEsIG9zamXEh2FqIHByaXBhZG5vc3Rp',
            'IGkgemFqZWRuacSNa2kgcmFzdC4iXX0sInJlZmVyZW5jZXMiOnsibGFiZWwiOiJOYcWhZSByZWZlcmVuY2UiLCJ0aXRsZSI6IlBvdmplcmVuamUga2xpamVu',
            'YXRhIHBvdHZyxJF1amUga3ZhbGl0ZXR1IG5hxaFlZyByYWRhIiwia2lja2VyIjoiUmVmZXJlbmNlIiwiYm9keV9odG1sIjoiPHA+UG92amVyZW5qZSA3MDAg',
            'a2xpamVuYXRhIGl6IHJhemxpxI1pdGloIGluZHVzdHJpamEgaSBzZWt0b3JhIHBvdHZyZGEgamUga3ZhbGl0ZXRlIGkgc3RydcSNbm9zdGkga29qdSBzdmFr',
            'b2RuZXZubyBwcnXFvmFtby48L3A+PHA+U3VyYcSRdWplbW8gcyBtYWxpbSwgc3JlZG5qaW0gaSB2ZWxpa2ltIHBvZHV6ZcSHaW1hIGtvamltYSBwcnXFvmFt',
            'byBwb2RyxaFrdSB1IHBvZHJ1xI1qdSByYcSNdW5vdm9kc3R2YSwgcmV2aXppamUgaSBwb3Nsb3Zub2cgc2F2amV0b3ZhbmphLjwvcD48cD5OYcWhaSBkdWdv',
            'cm/EjW5pIG9kbm9zaSBzIGtsaWplbnRpbWEgdGVtZWxqZSBzZSBuYSBwb3ZqZXJlbmp1LCBkb3N0dXBub3N0aSwgc3RydcSNbm9zdGkgaSByYXp1bWlqZXZh',
            'bmp1IG5qaWhvdmloIHBvc2xvdm5paCBjaWxqZXZhLjwvcD48cD5Vc3BqZWggbmHFoWloIGtsaWplbmF0YSB1amVkbm8gamUgaSBuYWp2ZcSHYSBwb3R2cmRh',
            'IG5hxaFlZyByYWRhLjwvcD4iLCJwYXJhZ3JhcGhzIjpbIlBvdmplcmVuamUgNzAwIGtsaWplbmF0YSBpeiByYXpsacSNaXRpaCBpbmR1c3RyaWphIGkgc2Vr',
            'dG9yYSBwb3R2cmRhIGplIGt2YWxpdGV0ZSBpIHN0cnXEjW5vc3RpIGtvanUgc3Zha29kbmV2bm8gcHJ1xb5hbW8uIiwiU3VyYcSRdWplbW8gcyBtYWxpbSwg',
            'c3JlZG5qaW0gaSB2ZWxpa2ltIHBvZHV6ZcSHaW1hIGtvamltYSBwcnXFvmFtbyBwb2RyxaFrdSB1IHBvZHJ1xI1qdSByYcSNdW5vdm9kc3R2YSwgcmV2aXpp',
            'amUgaSBwb3Nsb3Zub2cgc2F2amV0b3ZhbmphLiIsIk5hxaFpIGR1Z29yb8SNbmkgb2Rub3NpIHMga2xpamVudGltYSB0ZW1lbGplIHNlIG5hIHBvdmplcmVu',
            'anUsIGRvc3R1cG5vc3RpLCBzdHJ1xI1ub3N0aSBpIHJhenVtaWpldmFuanUgbmppaG92aWggcG9zbG92bmloIGNpbGpldmEuIiwiVXNwamVoIG5hxaFpaCBr',
            'bGlqZW5hdGEgdWplZG5vIGplIGkgbmFqdmXEh2EgcG90dnJkYSBuYcWhZWcgcmFkYS4iXSwiYnV0dG9uX2xhYmVsIjoiU3ZlIHJlZmVyZW5jZSJ9LCJyZXNw',
            'b25zaWJpbGl0eSI6eyJxdW90ZSI6IlZqZXJ1amVtbyBkYSB1c3BqZWggaW1hIG5hanZlxId1IHZyaWplZG5vc3Qga2FkYSBzdHZhcmEgcHJpbGlrZSB6YSBk',
            'cnVnZS4iLCJ0aXRsZSI6IkFVWElMSVVNIENBUElUQUxJUyAtIHVsYWdhbmplIHUgYnVkdcSHbm9zdCIsImtpY2tlciI6IkRydcWhdHZlbm8gb2Rnb3Zvcm5v',
            'IHBvc2xvdmFuamUiLCJjdGFfdGV4dCI6IlBveml2YW1vIHBvamVkaW5jZSwgcGFydG5lcmUgaSB0dnJ0a2UgZGEgbmFtIHNlIHByaWRydcW+ZSB1IHN0dmFy',
            'YW5qdSBub3ZpaCBwcmlsaWthIHphIG1sYWRlIGkgemFqZWRubyBwb21vZ25lbW8gZ3JhZGl0aSBib2xqdSBidWR1xIdub3N0LiIsImJvZHlfaHRtbCI6Ijxw',
            'PlphdG8gc21vIHBva3JlbnVsaSBBVVhJTElVTSBDQVBJVEFMSVMgLSBpbmljaWphdGl2dSB1c21qZXJlbnUgbmEgc3RpcGVuZGlyYW5qZSB1xI1lbmlrYSBp',
            'IHBydcW+YW5qZSBwb2RyxaFrZSBtbGFkaW1hIGtyb3ogb2JyYXpvdmFuamUsIHJhenZvaiBpIGZpbmFuY2lqc2t1IHBpc21lbm9zdC48L3A+PHA+TmHFoSBj',
            'aWxqIGplIHBvbW/Eh2kgdGFsZW50aXJhbmltIGkgcGVyc3Bla3Rpdm5pbSBtbGFkaW0gbGp1ZGltYSBkYSBsYWvFoWUgb3N0dmFyZSBzdm9qIHBvdGVuY2lq',
            'YWwsIGJleiBvYnppcmEgbmEgb2tvbG5vc3RpIGl6IGtvamloIGRvbGF6ZS48L3A+PHA+VmplcnVqZW1vIGRhIHVsYWdhbmplIHUgem5hbmplLCBwcmlsaWtl',
            'IGkgbWxhZGUgZ2VuZXJhY2lqZSBkdWdvcm/EjW5vIG1pamVuamEgemFqZWRuaWN1IG5hIGJvbGplLjwvcD48cD5BVVhJTElVTSBDQVBJVEFMSVMgbmlqZSBz',
            'YW1vIHByb2pla3QgLSB0byBqZSBuYcSNaW4gbmEga29qaSDFvmVsaW1vIHZyYcSHYXRpIHphamVkbmljaSBpIHN0dmFyYXRpIGtvbmtyZXRhbiwgZHVnb3Jv',
            'xI1hbiB1dGplY2FqLjwvcD4iLCJjdGFfaW50cm8iOiLFvWVsaXRlIGJpdGkgZGlvIG92ZSBwcmnEjWU/IiwiY3RhX3N0YXR1cyI6Ik90dm9yZW5pIHNtbyB6',
            'YSByYXpnb3ZvciBpIG5vdmEgcGFydG5lcnN0dmEuIiwicGFyYWdyYXBocyI6WyJaYXRvIHNtbyBwb2tyZW51bGkgQVVYSUxJVU0gQ0FQSVRBTElTIC0gaW5p',
            'Y2lqYXRpdnUgdXNtamVyZW51IG5hIHN0aXBlbmRpcmFuamUgdcSNZW5pa2EgaSBwcnXFvmFuamUgcG9kcsWha2UgbWxhZGltYSBrcm96IG9icmF6b3Zhbmpl',
            'LCByYXp2b2ogaSBmaW5hbmNpanNrdSBwaXNtZW5vc3QuIiwiTmHFoSBjaWxqIGplIHBvbW/Eh2kgdGFsZW50aXJhbmltIGkgcGVyc3Bla3Rpdm5pbSBtbGFk',
            'aW0gbGp1ZGltYSBkYSBsYWvFoWUgb3N0dmFyZSBzdm9qIHBvdGVuY2lqYWwsIGJleiBvYnppcmEgbmEgb2tvbG5vc3RpIGl6IGtvamloIGRvbGF6ZS4iLCJW',
            'amVydWplbW8gZGEgdWxhZ2FuamUgdSB6bmFuamUsIHByaWxpa2UgaSBtbGFkZSBnZW5lcmFjaWplIGR1Z29yb8SNbm8gbWlqZW5qYSB6YWplZG5pY3UgbmEg',
            'Ym9samUuIiwiQVVYSUxJVU0gQ0FQSVRBTElTIG5pamUgc2FtbyBwcm9qZWt0IC0gdG8gamUgbmHEjWluIG5hIGtvamkgxb5lbGltbyB2cmHEh2F0aSB6YWpl',
            'ZG5pY2kgaSBzdHZhcmF0aSBrb25rcmV0YW4sIGR1Z29yb8SNYW4gdXRqZWNhai4iXSwiY3RhX2NhcmRfdGl0bGUiOiJaYWplZG5vIG1vxb5lbW8gdmnFoWUu',
            'IiwiY3RhX2J1dHRvbl9sYWJlbCI6IktvbnRha3RpcmFqdGUgbmFzIn19'
        )
    ) USING utf8mb4), '$')
    ),
    updated_at = NOW()
WHERE page_id = @ac_about_page_id
  AND locale = 'hr';

UPDATE content_info_page_translations
SET
    payload = JSON_SET(
        COALESCE(payload, JSON_OBJECT()),
        '$.about_page',
        JSON_EXTRACT(CONVERT(FROM_BASE64(
        CONCAT(
            'eyJ3aHkiOnsicXVvdGUiOiJXZSBiZWxpZXZlIHN1Y2Nlc3NmdWwgYnVzaW5lc3MgaXMgbm90IGJ1aWx0IG9ubHkgb24gbnVtYmVycywgYnV0IGFsc28gb24g',
            'cXVhbGl0eSByZWxhdGlvbnNoaXBzLCBjbGVhciBzdHJhdGVneSBhbmQgdGltZWx5IGRlY2lzaW9ucy4iLCJ0aXRsZSI6IlN1cHBvcnQgZm9yIHNlY3VyZSwg',
            'cXVhbGl0eSBhbmQgc3VzdGFpbmFibGUgYnVzaW5lc3MiLCJraWNrZXIiOiJXaHkgd2UgZXhpc3QiLCJib2R5X2h0bWwiOiI8cD5XZSBleGlzdCB0byBoZWxw',
            'IGVudHJlcHJlbmV1cnMgYnVpbGQgbW9yZSBzZWN1cmUsIGhpZ2hlci1xdWFsaXR5IGFuZCBtb3JlIHN1c3RhaW5hYmxlIGJ1c2luZXNzZXMuPC9wPjxwPk91',
            'ciBtaXNzaW9uIGlzIHRvIGJlIGEgcmVsaWFibGUgcGFydG5lciB0aGF0IHByb3ZpZGVzIGV4cGVydCBzdXBwb3J0IGluIGtleSBidXNpbmVzcyBhcmVhcyAt',
            'IGZyb20gZmluYW5jZSBhbmQgYWNjb3VudGluZyB0byBzdHJhdGVnaWMgZGV2ZWxvcG1lbnQsIGF1ZGl0IGFuZCBFVSBmdW5kcy48L3A+PHA+T3VyIGdvYWwg',
            'aXMgbm90IG9ubHkgdG8gZm9sbG93IG91ciBjbGllbnRzIGJ1c2luZXNzLCBidXQgdG8gYWN0aXZlbHkgY29udHJpYnV0ZSB0byB0aGVpciBncm93dGggYW5k',
            'IGxvbmctdGVybSBzdGFiaWxpdHkuPC9wPiIsInBhcmFncmFwaHMiOlsiV2UgZXhpc3QgdG8gaGVscCBlbnRyZXByZW5ldXJzIGJ1aWxkIG1vcmUgc2VjdXJl',
            'LCBoaWdoZXItcXVhbGl0eSBhbmQgbW9yZSBzdXN0YWluYWJsZSBidXNpbmVzc2VzLiIsIk91ciBtaXNzaW9uIGlzIHRvIGJlIGEgcmVsaWFibGUgcGFydG5l',
            'ciB0aGF0IHByb3ZpZGVzIGV4cGVydCBzdXBwb3J0IGluIGtleSBidXNpbmVzcyBhcmVhcyAtIGZyb20gZmluYW5jZSBhbmQgYWNjb3VudGluZyB0byBzdHJh',
            'dGVnaWMgZGV2ZWxvcG1lbnQsIGF1ZGl0IGFuZCBFVSBmdW5kcy4iLCJPdXIgZ29hbCBpcyBub3Qgb25seSB0byBmb2xsb3cgb3VyIGNsaWVudHMgYnVzaW5l',
            'c3MsIGJ1dCB0byBhY3RpdmVseSBjb250cmlidXRlIHRvIHRoZWlyIGdyb3d0aCBhbmQgbG9uZy10ZXJtIHN0YWJpbGl0eS4iXX0sImhlcm8iOnsibGVhZCI6',
            'IkZyb20gZXhwZXJ0aXNlIGFuZCBleHBlcmllbmNlIHRvIGxvbmctdGVybSBjbGllbnQgcmVsYXRpb25zaGlwcy4iLCJ0aXRsZSI6Ik91ciBzdG9yeSIsImV5',
            'ZWJyb3ciOiJBYm91dCB1cyIsImltYWdlX2FsdCI6IiIsInN0YXRfbGFiZWwiOiJjbGllbnRzIHN1cHBvcnRlZCBieSBvdXIgdGVhbSIsInN0YXRfdmFsdWUi',
            'OiI3MDAifSwidGVhbSI6eyJib2R5IjoiT3VyIHRlYW0gYnJpbmdzIHRvZ2V0aGVyIHNwZWNpYWxpc3RzIGluIGFjY291bnRpbmcsIGF1ZGl0IGFuZCBidXNp',
            'bmVzcyBhZHZpc29yeSB3aG8gd29yayB0b2dldGhlciB0byBwcm92aWRlIHF1YWxpdHksIHRpbWVseSBhbmQgdGFpbG9yZWQgc29sdXRpb25zLiIsImludHJv',
            'IjoiQUxQSEEgQ0FQSVRBTElTIGlzIGEgc3Ryb25nIG11bHRpZGlzY2lwbGluYXJ5IHRlYW0gb2YgZXhwZXJ0cyBzdXBwb3J0aW5nIGNsaWVudHMgZnJvbSBk',
            'aWZmZXJlbnQgaW5kdXN0cmllcyBhbmQgYnVzaW5lc3Mgc2VjdG9ycyBldmVyeSBkYXkuIiwibGFiZWwiOiJPdXIgdGVhbSIsInN0YXRzIjpbeyJsYWJlbCI6',
            'ImV4cGVydHMiLCJ2YWx1ZSI6Ijc1In0seyJsYWJlbCI6Im1hbmFnZW1lbnQgYm9hcmQgbWVtYmVycyIsInZhbHVlIjoiOSJ9LHsibGFiZWwiOiJjbGllbnRz',
            'IiwidmFsdWUiOiI3MDAifSx7ImxhYmVsIjoib2ZmaWNlcyBpbiBaYWdyZWIsIFZpbmtvdmNpIGFuZCBSaWpla2EiLCJ2YWx1ZSI6IjMifV0sInRpdGxlIjoi',
            'VGhlIHBlb3BsZSBiZWhpbmQgQUxQSEEgQ0FQSVRBTElTIiwia2lja2VyIjoiVEVBTSIsImJvZHlfaHRtbCI6IjxwPkFMUEhBIENBUElUQUxJUyBpcyBhIHN0',
            'cm9uZyBtdWx0aWRpc2NpcGxpbmFyeSB0ZWFtIG9mIGV4cGVydHMgc3VwcG9ydGluZyBjbGllbnRzIGZyb20gZGlmZmVyZW50IGluZHVzdHJpZXMgYW5kIGJ1',
            'c2luZXNzIHNlY3RvcnMgZXZlcnkgZGF5LjwvcD48cD5PdXIgdGVhbSBicmluZ3MgdG9nZXRoZXIgc3BlY2lhbGlzdHMgaW4gYWNjb3VudGluZywgYXVkaXQg',
            'YW5kIGJ1c2luZXNzIGFkdmlzb3J5IHdobyB3b3JrIHRvZ2V0aGVyIHRvIHByb3ZpZGUgcXVhbGl0eSwgdGltZWx5IGFuZCB0YWlsb3JlZCBzb2x1dGlvbnMu',
            'PC9wPiIsImJ1dHRvbl9sYWJlbCI6Ik1lZXQgdGhlIGZ1bGwgdGVhbSJ9LCJzdG9yeSI6eyJ0aXRsZSI6IkEgcGFydG5lciBmb3IgY29uZmlkZW50IGJ1c2lu',
            'ZXNzIGRlY2lzaW9ucyIsImtpY2tlciI6Ik91ciBzdG9yeSIsImJvZHlfaHRtbCI6IjxwPkFMUEhBIENBUElUQUxJUyB3YXMgY3JlYXRlZCBmcm9tIHRoZSBk',
            'ZXNpcmUgdG8gZ2l2ZSBlbnRyZXByZW5ldXJzIG1vcmUgdGhhbiBzdGFuZGFyZCBidXNpbmVzcyBzdXBwb3J0LiBGcm9tIHRoZSBiZWdpbm5pbmcsIHdlIGhh',
            'dmUgYnVpbHQgYSBjb21wYW55IHRoYXQgY29tYmluZXMgZXhwZXJ0aXNlLCBleHBlcmllbmNlIGFuZCBhbiB1bmRlcnN0YW5kaW5nIG9mIHRoZSByZWFsIGNo',
            'YWxsZW5nZXMgZmFjZWQgYnkgZW50cmVwcmVuZXVycywgZmFtaWx5IGJ1c2luZXNzZXMgYW5kIGdyb3dpbmcgb3JnYW5pc2F0aW9ucy48L3A+PHA+T3ZlciB0',
            'aGUgeWVhcnMsIHdlIGhhdmUgZGV2ZWxvcGVkIGEgbXVsdGlkaXNjaXBsaW5hcnkgdGVhbSBvZiBzcGVjaWFsaXN0cyBpbiBhY2NvdW50aW5nLCBmaW5hbmNl',
            'LCB0YXgsIGF1ZGl0IGFuZCBFVSBmdW5kcywgZm9jdXNlZCBvbiBwcm92aWRpbmcgY29tcGxldGUgYW5kIGxvbmctdGVybSBzb2x1dGlvbnMgZm9yIG91ciBj',
            'bGllbnRzLjwvcD48cD5Ub2RheSwgQUxQSEEgQ0FQSVRBTElTIHdvcmtzIGFzIGEgcGFydG5lciB0aGF0IGdpdmVzIGNsaWVudHMgY29uZmlkZW5jZSBpbiBk',
            'ZWNpc2lvbi1tYWtpbmcsIHN0YWJpbGl0eSBpbiBvcGVyYXRpb25zIGFuZCBzdXBwb3J0IGFjcm9zcyBhbGwgc3RhZ2VzIG9mIGRldmVsb3BtZW50IC0gZnJv',
            'bSBkYWlseSBvcGVyYXRpb25zIHRvIHN0cmF0ZWdpYyBkZWNpc2lvbnMgYW5kIGJ1c2luZXNzIHN1Y2Nlc3Npb24uPC9wPjxwPk91ciBzdG9yeSBpcyBidWls',
            'dCBvbiB0cnVzdCwgZXhwZXJ0aXNlIGFuZCBsb25nLXRlcm0gcmVsYXRpb25zaGlwcyB3aXRoIGNsaWVudHMuIFRoYXQgaXMgd2h5IG1hbnkgb2YgdGhlbSBo',
            'YXZlIGJlZW4gZ3Jvd2luZyB3aXRoIHVzIGZvciB5ZWFycy48L3A+IiwicGFyYWdyYXBocyI6WyJBTFBIQSBDQVBJVEFMSVMgd2FzIGNyZWF0ZWQgZnJvbSB0',
            'aGUgZGVzaXJlIHRvIGdpdmUgZW50cmVwcmVuZXVycyBtb3JlIHRoYW4gc3RhbmRhcmQgYnVzaW5lc3Mgc3VwcG9ydC4gRnJvbSB0aGUgYmVnaW5uaW5nLCB3',
            'ZSBoYXZlIGJ1aWx0IGEgY29tcGFueSB0aGF0IGNvbWJpbmVzIGV4cGVydGlzZSwgZXhwZXJpZW5jZSBhbmQgYW4gdW5kZXJzdGFuZGluZyBvZiB0aGUgcmVh',
            'bCBjaGFsbGVuZ2VzIGZhY2VkIGJ5IGVudHJlcHJlbmV1cnMsIGZhbWlseSBidXNpbmVzc2VzIGFuZCBncm93aW5nIG9yZ2FuaXNhdGlvbnMuIiwiT3ZlciB0',
            'aGUgeWVhcnMsIHdlIGhhdmUgZGV2ZWxvcGVkIGEgbXVsdGlkaXNjaXBsaW5hcnkgdGVhbSBvZiBzcGVjaWFsaXN0cyBpbiBhY2NvdW50aW5nLCBmaW5hbmNl',
            'LCB0YXgsIGF1ZGl0IGFuZCBFVSBmdW5kcywgZm9jdXNlZCBvbiBwcm92aWRpbmcgY29tcGxldGUgYW5kIGxvbmctdGVybSBzb2x1dGlvbnMgZm9yIG91ciBj',
            'bGllbnRzLiIsIlRvZGF5LCBBTFBIQSBDQVBJVEFMSVMgd29ya3MgYXMgYSBwYXJ0bmVyIHRoYXQgZ2l2ZXMgY2xpZW50cyBjb25maWRlbmNlIGluIGRlY2lz',
            'aW9uLW1ha2luZywgc3RhYmlsaXR5IGluIG9wZXJhdGlvbnMgYW5kIHN1cHBvcnQgYWNyb3NzIGFsbCBzdGFnZXMgb2YgZGV2ZWxvcG1lbnQgLSBmcm9tIGRh',
            'aWx5IG9wZXJhdGlvbnMgdG8gc3RyYXRlZ2ljIGRlY2lzaW9ucyBhbmQgYnVzaW5lc3Mgc3VjY2Vzc2lvbi4iLCJPdXIgc3RvcnkgaXMgYnVpbHQgb24gdHJ1',
            'c3QsIGV4cGVydGlzZSBhbmQgbG9uZy10ZXJtIHJlbGF0aW9uc2hpcHMgd2l0aCBjbGllbnRzLiBUaGF0IGlzIHdoeSBtYW55IG9mIHRoZW0gaGF2ZSBiZWVu',
            'IGdyb3dpbmcgd2l0aCB1cyBmb3IgeWVhcnMuIl19LCJ2YWx1ZXMiOnsiaW50cm8iOiJBdCBBTFBIQSBDQVBJVEFMSVMsIHZhbHVlcyBhcmUgbm90IG9ubHkg',
            'd29yZHMuIFRoZXkgZGVmaW5lIGhvdyB3ZSB0aGluaywgd29yayBhbmQgYnVpbGQgcmVsYXRpb25zaGlwcyB3aXRoIGVhY2ggb3RoZXIgYW5kIHdpdGggb3Vy',
            'IGNsaWVudHMuIiwiaXRlbXMiOlt7ImxlYWQiOiJXZSB2YWx1ZSBwZW9wbGUgd2hvIHdhbnQgdG8gbGVhcm4sIGFzaywgZXhwbG9yZSBhbmQgZGV2ZWxvcCBx',
            'dWlja2x5LiIsInRpdGxlIjoiTGVhcm4gZmFzdCIsImJvZHlfaHRtbCI6IjxwPldlIHZhbHVlIHBlb3BsZSB3aG8gd2FudCB0byBsZWFybiwgYXNrLCBleHBs',
            'b3JlIGFuZCBkZXZlbG9wIHF1aWNrbHkuPC9wPjxwPldlIHdvcmsgaW4gYW4gZW52aXJvbm1lbnQgdGhhdCBrZWVwcyBjaGFuZ2luZyAtIG1hcmtldHMsIGxh',
            'd3MsIHRlY2hub2xvZ3kgYW5kIGNsaWVudCBuZWVkcy4gVGhlIGFiaWxpdHkgdG8gbGVhcm4gcXVpY2tseSBpcyBvbmUgb2YgdGhlIG1vc3QgaW1wb3J0YW50',
            'IHN0cmVuZ3RocyB3ZSBjYW4gaGF2ZSBhcyBhIHRlYW0uPC9wPjxwPk5vdCBrbm93aW5nIGlzIG5vdCB0aGUgcHJvYmxlbS4gTm90IHdhbnRpbmcgdG8gbGVh',
            'cm4gaXMuPC9wPjxwPlRoYXQgaXMgd2h5IHdlIHNoYXJlIGtub3dsZWRnZSwgbGVhcm4gZnJvbSBvbmUgYW5vdGhlciwgZ3JvdyB0aHJvdWdoIHByYWN0aWNl',
            'IGFuZCB0YWtlIHJlc3BvbnNpYmlsaXR5IHdpdGhvdXQgd2FpdGluZyBmb3IgYSBwZXJmZWN0IG1vbWVudC48L3A+IiwicGFyYWdyYXBocyI6WyJXZSB3b3Jr',
            'IGluIGFuIGVudmlyb25tZW50IHRoYXQga2VlcHMgY2hhbmdpbmcgLSBtYXJrZXRzLCBsYXdzLCB0ZWNobm9sb2d5IGFuZCBjbGllbnQgbmVlZHMuIFRoZSBh',
            'YmlsaXR5IHRvIGxlYXJuIHF1aWNrbHkgaXMgb25lIG9mIHRoZSBtb3N0IGltcG9ydGFudCBzdHJlbmd0aHMgd2UgY2FuIGhhdmUgYXMgYSB0ZWFtLiIsIk5v',
            'dCBrbm93aW5nIGlzIG5vdCB0aGUgcHJvYmxlbS4gTm90IHdhbnRpbmcgdG8gbGVhcm4gaXMuIiwiVGhhdCBpcyB3aHkgd2Ugc2hhcmUga25vd2xlZGdlLCBs',
            'ZWFybiBmcm9tIG9uZSBhbm90aGVyLCBncm93IHRocm91Z2ggcHJhY3RpY2UgYW5kIHRha2UgcmVzcG9uc2liaWxpdHkgd2l0aG91dCB3YWl0aW5nIGZvciBh',
            'IHBlcmZlY3QgbW9tZW50LiJdfSx7ImxlYWQiOiJXZSBkbyBub3QgYmVsaWV2ZSBpbiBhIGN1bHR1cmUgd2hlcmUgc3RheWluZyBsYXRlIGlzIHRoZSBtZWFz',
            'dXJlIG9mIHdvcmsuIiwidGl0bGUiOiJXb3JrIHNtYXJ0LCBub3QgaGFyZCIsImJvZHlfaHRtbCI6IjxwPldlIGRvIG5vdCBiZWxpZXZlIGluIGEgY3VsdHVy',
            'ZSB3aGVyZSBzdGF5aW5nIGxhdGUgaXMgdGhlIG1lYXN1cmUgb2Ygd29yay48L3A+PHA+V2UgYmVsaWV2ZSBpbiB0aG91Z2h0ZnVsIHdvcmsuIFRoYXQgbWVh',
            'bnMgcGxhbm5pbmcgYWhlYWQsIHNldHRpbmcgcHJpb3JpdGllcywgbG9va2luZyBmb3IgYmV0dGVyIHNvbHV0aW9ucyBhbmQgYXZvaWRpbmcgaGFiaXRzIHRo',
            'YXQgZXhpc3Qgb25seSBiZWNhdXNlIHRoZXkgaGF2ZSBhbHdheXMgYmVlbiBkb25lIHRoYXQgd2F5LjwvcD48cD5XZSB2YWx1ZSBwZW9wbGUgd2hvIHJlY29n',
            'bmlzZSBwcm9ibGVtcywgYW5kIGV2ZW4gbW9yZSB0aG9zZSB3aG8gcHJvcG9zZSBzb2x1dGlvbnMuIFByb2R1Y3Rpdml0eSBpcyBub3QgY2hhb3MsIGJ1dCBm',
            'b2N1cy48L3A+PHA+V2Ugd2FudCB0byBjcmVhdGUgcmVzdWx0cyB3aXRob3V0IHVubmVjZXNzYXJ5IGNvbXBsZXhpdHkgLSByZXNwb25zaWJseSwgY2xlYXJs',
            'eSBhbmQgd2l0aCBxdWFsaXR5LjwvcD4iLCJwYXJhZ3JhcGhzIjpbIldlIGJlbGlldmUgaW4gdGhvdWdodGZ1bCB3b3JrLiBUaGF0IG1lYW5zIHBsYW5uaW5n',
            'IGFoZWFkLCBzZXR0aW5nIHByaW9yaXRpZXMsIGxvb2tpbmcgZm9yIGJldHRlciBzb2x1dGlvbnMgYW5kIGF2b2lkaW5nIGhhYml0cyB0aGF0IGV4aXN0IG9u',
            'bHkgYmVjYXVzZSB0aGV5IGhhdmUgYWx3YXlzIGJlZW4gZG9uZSB0aGF0IHdheS4iLCJXZSB2YWx1ZSBwZW9wbGUgd2hvIHJlY29nbmlzZSBwcm9ibGVtcywg',
            'YW5kIGV2ZW4gbW9yZSB0aG9zZSB3aG8gcHJvcG9zZSBzb2x1dGlvbnMuIFByb2R1Y3Rpdml0eSBpcyBub3QgY2hhb3MsIGJ1dCBmb2N1cy4iLCJXZSB3YW50',
            'IHRvIGNyZWF0ZSByZXN1bHRzIHdpdGhvdXQgdW5uZWNlc3NhcnkgY29tcGxleGl0eSAtIHJlc3BvbnNpYmx5LCBjbGVhcmx5IGFuZCB3aXRoIHF1YWxpdHku',
            'Il19LHsibGVhZCI6IlBlb3BsZSBhcmUgYWx3YXlzIG1vcmUgaW1wb3J0YW50IHRoYW4gcHJvY2Vzcy4iLCJ0aXRsZSI6IlJlbGF0aW9uc2hpcCBvdmVyIHRy',
            'YW5zYWN0aW9uIiwiYm9keV9odG1sIjoiPHA+UGVvcGxlIGFyZSBhbHdheXMgbW9yZSBpbXBvcnRhbnQgdGhhbiBwcm9jZXNzLjwvcD48cD5XZSBkbyBub3Qg',
            'YnVpbGQgcmVsYXRpb25zaGlwcyB0aGF0IGxhc3Qgb25lIHByb2plY3Qgb3Igb25lIGVtYWlsLiBXZSBidWlsZCBwYXJ0bmVyc2hpcHMuPC9wPjxwPlRoYXQg',
            'YXBwbGllcyB0byBjbGllbnRzIGFuZCB0byBvdXIgdGVhbS4gVHJ1c3QgaXMgYnVpbHQgdGhyb3VnaCBhdmFpbGFiaWxpdHksIGhvbmVzdHksIHF1YWxpdHkg',
            'Y29tbXVuaWNhdGlvbiBhbmQgYmVpbmcgcHJlc2VudCB3aGVuIGl0IG1hdHRlcnMuPC9wPjxwPk51bWJlcnMgbWF0dGVyLCBidXQgcGVvcGxlIGFyZSB0aGUg',
            'cmVhc29uIHdvcmsgaGFzIG1lYW5pbmcuPC9wPiIsInBhcmFncmFwaHMiOlsiV2UgZG8gbm90IGJ1aWxkIHJlbGF0aW9uc2hpcHMgdGhhdCBsYXN0IG9uZSBw',
            'cm9qZWN0IG9yIG9uZSBlbWFpbC4gV2UgYnVpbGQgcGFydG5lcnNoaXBzLiIsIlRoYXQgYXBwbGllcyB0byBjbGllbnRzIGFuZCB0byBvdXIgdGVhbS4gVHJ1',
            'c3QgaXMgYnVpbHQgdGhyb3VnaCBhdmFpbGFiaWxpdHksIGhvbmVzdHksIHF1YWxpdHkgY29tbXVuaWNhdGlvbiBhbmQgYmVpbmcgcHJlc2VudCB3aGVuIGl0',
            'IG1hdHRlcnMuIiwiTnVtYmVycyBtYXR0ZXIsIGJ1dCBwZW9wbGUgYXJlIHRoZSByZWFzb24gd29yayBoYXMgbWVhbmluZy4iXX1dLCJsYWJlbCI6Ik91ciB2',
            'YWx1ZXMiLCJ0aXRsZSI6IlNpbXBsZSBwcmluY2lwbGVzIHRoYXQgZ3VpZGUgb3VyIHdvcmsiLCJraWNrZXIiOiJPdXIgdmFsdWVzIn0sImN1bHR1cmUiOnsi',
            'cXVvdGUiOiJBdCBBTFBIQSBDQVBJVEFMSVMsIHdlIGJlbGlldmUgcXVhbGl0eSBidXNpbmVzcyBzdGFydHMgd2l0aCBxdWFsaXR5IHJlbGF0aW9uc2hpcHMu',
            'IiwidGl0bGUiOiJRdWFsaXR5IGJ1c2luZXNzIHN0YXJ0cyB3aXRoIHF1YWxpdHkgcmVsYXRpb25zaGlwcyIsImtpY2tlciI6Ik91ciBjdWx0dXJlIiwiYm9k',
            'eV9odG1sIjoiPHA+V2UgYnVpbGQgYSBjdWx0dXJlIHRoYXQgZW5jb3VyYWdlcyBjb2xsYWJvcmF0aW9uLCBwcm9mZXNzaW9uYWwgZGV2ZWxvcG1lbnQsIG9w',
            'ZW4gY29tbXVuaWNhdGlvbiBhbmQgbXV0dWFsIHJlc3BlY3QuPC9wPjxwPldlIGVuY291cmFnZSBjb250aW51b3VzIGxlYXJuaW5nLCBrbm93bGVkZ2Ugc2hh',
            'cmluZyBhbmQgdGhlIGRldmVsb3BtZW50IG9mIG5ldyBpZGVhcyBiZWNhdXNlIHdlIGJlbGlldmUgcGVvcGxlIG1ha2UgdGhlIGJpZ2dlc3QgZGlmZmVyZW5j',
            'ZS48L3A+PHA+QWxvbmdzaWRlIHByb2Zlc3Npb25hbGlzbSwgYSBwb3NpdGl2ZSB3b3JraW5nIGF0bW9zcGhlcmUsIGJlbG9uZ2luZyBhbmQgc2hhcmVkIGdy',
            'b3d0aCBhcmUgZXF1YWxseSBpbXBvcnRhbnQgdG8gdXMuPC9wPiIsInBhcmFncmFwaHMiOlsiV2UgYnVpbGQgYSBjdWx0dXJlIHRoYXQgZW5jb3VyYWdlcyBj',
            'b2xsYWJvcmF0aW9uLCBwcm9mZXNzaW9uYWwgZGV2ZWxvcG1lbnQsIG9wZW4gY29tbXVuaWNhdGlvbiBhbmQgbXV0dWFsIHJlc3BlY3QuIiwiV2UgZW5jb3Vy',
            'YWdlIGNvbnRpbnVvdXMgbGVhcm5pbmcsIGtub3dsZWRnZSBzaGFyaW5nIGFuZCB0aGUgZGV2ZWxvcG1lbnQgb2YgbmV3IGlkZWFzIGJlY2F1c2Ugd2UgYmVs',
            'aWV2ZSBwZW9wbGUgbWFrZSB0aGUgYmlnZ2VzdCBkaWZmZXJlbmNlLiIsIkFsb25nc2lkZSBwcm9mZXNzaW9uYWxpc20sIGEgcG9zaXRpdmUgd29ya2luZyBh',
            'dG1vc3BoZXJlLCBiZWxvbmdpbmcgYW5kIHNoYXJlZCBncm93dGggYXJlIGVxdWFsbHkgaW1wb3J0YW50IHRvIHVzLiJdfSwicmVmZXJlbmNlcyI6eyJsYWJl',
            'bCI6Ik91ciByZWZlcmVuY2VzIiwidGl0bGUiOiJDbGllbnQgdHJ1c3QgY29uZmlybXMgdGhlIHF1YWxpdHkgb2Ygb3VyIHdvcmsiLCJraWNrZXIiOiJSZWZl',
            'cmVuY2VzIiwiYm9keV9odG1sIjoiPHA+VGhlIHRydXN0IG9mIDcwMCBjbGllbnRzIGFjcm9zcyBkaWZmZXJlbnQgaW5kdXN0cmllcyBhbmQgc2VjdG9ycyBj',
            'b25maXJtcyB0aGUgcXVhbGl0eSBhbmQgZXhwZXJ0aXNlIHdlIHByb3ZpZGUgZXZlcnkgZGF5LjwvcD48cD5XZSB3b3JrIHdpdGggc21hbGwsIG1lZGl1bS1z',
            'aXplZCBhbmQgbGFyZ2UgY29tcGFuaWVzLCBzdXBwb3J0aW5nIHRoZW0gaW4gYWNjb3VudGluZywgYXVkaXQgYW5kIGJ1c2luZXNzIGFkdmlzb3J5LjwvcD48',
            'cD5PdXIgbG9uZy10ZXJtIGNsaWVudCByZWxhdGlvbnNoaXBzIGFyZSBiYXNlZCBvbiB0cnVzdCwgYXZhaWxhYmlsaXR5LCBleHBlcnRpc2UgYW5kIGFuIHVu',
            'ZGVyc3RhbmRpbmcgb2YgdGhlaXIgYnVzaW5lc3MgZ29hbHMuPC9wPjxwPlRoZSBzdWNjZXNzIG9mIG91ciBjbGllbnRzIGlzIGFsc28gdGhlIHN0cm9uZ2Vz',
            'dCBjb25maXJtYXRpb24gb2Ygb3VyIHdvcmsuPC9wPiIsInBhcmFncmFwaHMiOlsiVGhlIHRydXN0IG9mIDcwMCBjbGllbnRzIGFjcm9zcyBkaWZmZXJlbnQg',
            'aW5kdXN0cmllcyBhbmQgc2VjdG9ycyBjb25maXJtcyB0aGUgcXVhbGl0eSBhbmQgZXhwZXJ0aXNlIHdlIHByb3ZpZGUgZXZlcnkgZGF5LiIsIldlIHdvcmsg',
            'd2l0aCBzbWFsbCwgbWVkaXVtLXNpemVkIGFuZCBsYXJnZSBjb21wYW5pZXMsIHN1cHBvcnRpbmcgdGhlbSBpbiBhY2NvdW50aW5nLCBhdWRpdCBhbmQgYnVz',
            'aW5lc3MgYWR2aXNvcnkuIiwiT3VyIGxvbmctdGVybSBjbGllbnQgcmVsYXRpb25zaGlwcyBhcmUgYmFzZWQgb24gdHJ1c3QsIGF2YWlsYWJpbGl0eSwgZXhw',
            'ZXJ0aXNlIGFuZCBhbiB1bmRlcnN0YW5kaW5nIG9mIHRoZWlyIGJ1c2luZXNzIGdvYWxzLiIsIlRoZSBzdWNjZXNzIG9mIG91ciBjbGllbnRzIGlzIGFsc28g',
            'dGhlIHN0cm9uZ2VzdCBjb25maXJtYXRpb24gb2Ygb3VyIHdvcmsuIl0sImJ1dHRvbl9sYWJlbCI6IkFsbCByZWZlcmVuY2VzIn0sInJlc3BvbnNpYmlsaXR5',
            'Ijp7InF1b3RlIjoiV2UgYmVsaWV2ZSBzdWNjZXNzIGhhcyB0aGUgZ3JlYXRlc3QgdmFsdWUgd2hlbiBpdCBjcmVhdGVzIG9wcG9ydHVuaXRpZXMgZm9yIG90',
            'aGVycy4iLCJ0aXRsZSI6IkFVWElMSVVNIENBUElUQUxJUyAtIGludmVzdGluZyBpbiB0aGUgZnV0dXJlIiwia2lja2VyIjoiU29jaWFsIHJlc3BvbnNpYmls',
            'aXR5IiwiY3RhX3RleHQiOiJXZSBpbnZpdGUgaW5kaXZpZHVhbHMsIHBhcnRuZXJzIGFuZCBjb21wYW5pZXMgdG8gam9pbiB1cyBpbiBjcmVhdGluZyBuZXcg',
            'b3Bwb3J0dW5pdGllcyBmb3IgeW91bmcgcGVvcGxlIGFuZCBoZWxwaW5nIGJ1aWxkIGEgYmV0dGVyIGZ1dHVyZS4iLCJib2R5X2h0bWwiOiI8cD5UaGF0IGlz',
            'IHdoeSB3ZSBsYXVuY2hlZCBBVVhJTElVTSBDQVBJVEFMSVMgLSBhbiBpbml0aWF0aXZlIGZvY3VzZWQgb24gc2Nob2xhcnNoaXBzIGFuZCBzdXBwb3J0aW5n',
            'IHlvdW5nIHBlb3BsZSB0aHJvdWdoIGVkdWNhdGlvbiwgZGV2ZWxvcG1lbnQgYW5kIGZpbmFuY2lhbCBsaXRlcmFjeS48L3A+PHA+T3VyIGdvYWwgaXMgdG8g',
            'aGVscCB0YWxlbnRlZCBhbmQgcHJvbWlzaW5nIHlvdW5nIHBlb3BsZSByZWFjaCB0aGVpciBwb3RlbnRpYWwsIHJlZ2FyZGxlc3Mgb2YgdGhlaXIgY2lyY3Vt',
            'c3RhbmNlcy48L3A+PHA+QVVYSUxJVU0gQ0FQSVRBTElTIGlzIG5vdCBvbmx5IGEgcHJvamVjdC4gSXQgaXMgdGhlIHdheSB3ZSB3YW50IHRvIGdpdmUgYmFj',
            'ayB0byB0aGUgY29tbXVuaXR5IGFuZCBjcmVhdGUgY29uY3JldGUsIGxvbmctdGVybSBpbXBhY3QuPC9wPiIsImN0YV9pbnRybyI6IldvdWxkIHlvdSBsaWtl',
            'IHRvIGJlIHBhcnQgb2YgdGhpcyBzdG9yeT8iLCJjdGFfc3RhdHVzIjoiV2UgYXJlIG9wZW4gdG8gY29udmVyc2F0aW9ucyBhbmQgbmV3IHBhcnRuZXJzaGlw',
            'cy4iLCJwYXJhZ3JhcGhzIjpbIlRoYXQgaXMgd2h5IHdlIGxhdW5jaGVkIEFVWElMSVVNIENBUElUQUxJUyAtIGFuIGluaXRpYXRpdmUgZm9jdXNlZCBvbiBz',
            'Y2hvbGFyc2hpcHMgYW5kIHN1cHBvcnRpbmcgeW91bmcgcGVvcGxlIHRocm91Z2ggZWR1Y2F0aW9uLCBkZXZlbG9wbWVudCBhbmQgZmluYW5jaWFsIGxpdGVy',
            'YWN5LiIsIk91ciBnb2FsIGlzIHRvIGhlbHAgdGFsZW50ZWQgYW5kIHByb21pc2luZyB5b3VuZyBwZW9wbGUgcmVhY2ggdGhlaXIgcG90ZW50aWFsLCByZWdh',
            'cmRsZXNzIG9mIHRoZWlyIGNpcmN1bXN0YW5jZXMuIiwiQVVYSUxJVU0gQ0FQSVRBTElTIGlzIG5vdCBvbmx5IGEgcHJvamVjdC4gSXQgaXMgdGhlIHdheSB3',
            'ZSB3YW50IHRvIGdpdmUgYmFjayB0byB0aGUgY29tbXVuaXR5IGFuZCBjcmVhdGUgY29uY3JldGUsIGxvbmctdGVybSBpbXBhY3QuIl0sImN0YV9jYXJkX3Rp',
            'dGxlIjoiVG9nZXRoZXIsIHdlIGNhbiBkbyBtb3JlLiIsImN0YV9idXR0b25fbGFiZWwiOiJDb250YWN0IHVzIn19'
        )
    ) USING utf8mb4), '$')
    ),
    updated_at = NOW()
WHERE page_id = @ac_about_page_id
  AND locale = 'en';

-- Career page: warmer final copy and exact verified CMS payloads.
SET @ac_career_page_id := (
    SELECT id
    FROM content_info_pages
    WHERE code = 'career' OR layout = 'career'
    ORDER BY CASE WHEN code = 'career' THEN 0 ELSE 1 END, id
    LIMIT 1
);

UPDATE content_info_page_translations
SET
    payload = JSON_SET(
        COALESCE(payload, JSON_OBJECT()),
        '$.career_page',
        JSON_EXTRACT(CONVERT(FROM_BASE64(
        CONCAT(
            'eyJmb3JtIjp7ImN2IjoiVXBsb2FkIENWLWEiLCJlbWFpbCI6IkVtYWlsIiwiaW50cm8iOiJJc3B1bml0ZSBvc25vdm5lIHBvZGF0a2UgaSB1xI1pdGFqdGUg',
            'xb5pdm90b3BpcyBrYWtvIGJpc21vIHZhcyBtb2dsaSBrb250YWt0aXJhdGkga2FkYSBwcmVwb3puYW1vIHBvZHVkYXJhbmplIHMgb3R2b3JlbmltIHBvemlj',
            'aWphbWEuIiwidGl0bGUiOiJQb8WhYWxqaSBuYW0gc3ZvaiDFvml2b3RvcGlzIiwic3VibWl0IjoiUG/FoWFsamkgcHJpamF2dSIsImN2X2hlbHAiOiJQb2Ry',
            'xb5hbmkgZm9ybWF0aTogUERGLCBET0MgaSBET0NYLiBNYWtzaW1hbG5hIHZlbGnEjWluYSBkYXRvdGVrZSBqZSA1IE1CLiIsIm1lc3NhZ2UiOiJQb3J1a2Eg',
            'KG9wY2lvbmFsbm8pIiwiY3ZfZW1wdHkiOiJEYXRvdGVrYSBuaWplIG9kYWJyYW5hLiIsImN2X2J1dHRvbiI6Ik9kYWJlcmkgZGF0b3Rla3UiLCJsYXN0X25h',
            'bWUiOiJQcmV6aW1lIiwiZmlyc3RfbmFtZSI6IkltZSIsImFjY2VwdF90ZXJtcyI6IlNsYcW+ZW0gc2UgcyBvYnJhZG9tIG9zb2JuaWggcG9kYXRha2EgemEg',
            'cG90cmViZSBzZWxla2NpanNrb2cgcG9zdHVwa2EuIn0sImludHJvIjp7ImJvZHkiOlsiVHJhxb5pbW8gem5hdGnFvmVsam5lIGxqdWRlIGtvamkgxb5lbGUg',
            'dcSNaXRpLCBwcmV1emltYXRpIG9kZ292b3Jub3N0IGkgemFqZWRubyBzIG5hbWEgZ3JhZGl0aSBuZcWhdG8gZHVnb3JvxI1uby4iLCJBTFBIQSBDQVBJVEFM',
            'SVMgZGFuYXMgb2t1cGxqYSA3NSBzdHJ1xI1uamFrYSBpeiBwb2RydcSNamEgcmHEjXVub3ZvZHN0dmEsIGZpbmFuY2lqYSwgcmV2aXppamUsIEVVIGZvbmRv',
            'dmEgaSBzYXZqZXRvdmFuamEuIFUgbmHFoWVtIFNreSBPZmZpY2V1IHpuYW5qZSBkaWplbGltbyBvdHZvcmVubywgcG9tYcW+ZW1vIGplZG5pIGRydWdpbWEg',
            'aSBvemJpbGphbiBwb3NhbyBncmFkaW1vIHUgYXRtb3NmZXJpIHUga29qb2ogbGp1ZGkgbW9ndSBiaXRpIHN2b2ppLiIsIk9kIHBydm9nIGRhbmEgcmFkaXQg',
            'xIdlxaEgbmEgc3R2YXJuaW0gcG9zbG92bmltIGl6YXpvdmltYSwgdXogbWVudG9yYSwgcG9kcsWha3UgaXNrdXNuaWgga29sZWdhIGkgcHJvc3RvciBkYSBw',
            'cmVkbG/FvmnFoSBib2xqaSBuYcSNaW4gcmFkYS4gxb1lbGltbyBkYSBwb3NhbyBidWRlIG1qZXN0byBuYSBrb2plbSByYXN0ZcWhIGkga29qZW0gc2UgcmFk',
            'byB2cmHEh2HFoS4iXSwidGl0bGUiOiJNamVzdG8gZ2RqZSBsanVkaSBpIGthcmlqZXJlIHJhc3R1Iiwia2lja2VyIjoiUmFzdGVtbyB6YWplZG5vIiwiaGln',
            'aGxpZ2h0IjoiVHJhxb5pbW8gbGp1ZGUsIG5lIHNhbW8gxb5pdm90b3Bpc2UuIiwiaW1hZ2VfYWx0IjoiIiwic3RhdF9sYWJlbCI6InN0cnXEjW5qYWthIGl6',
            'IHJhxI11bm92b2RzdHZhLCBmaW5hbmNpamEsIHJldml6aWplIGkgc2F2amV0b3ZhbmphIiwic3RhdF92YWx1ZSI6Ijc1IiwiYnV0dG9uX2xhYmVsIjoiT1RW',
            'T1JFTkUgUE9aSUNJSkUiLCJ2YWx1ZXNfbGFiZWwiOiLFoHRvIG51ZGltbyIsInNlY3Rpb25fdGl0bGUiOiJLYXJpamVyYSB1IEFMUEhBIENBUElUQUxJU1Ui',
            'LCJoZXJvX2JvZHlfaHRtbCI6IjxwPkFMUEhBIENBUElUQUxJUyBkYW5hcyBva3VwbGphIDc1IHN0cnXEjW5qYWthIGl6IHBvZHJ1xI1qYSByYcSNdW5vdm9k',
            'c3R2YSwgZmluYW5jaWphLCByZXZpemlqZSwgRVUgZm9uZG92YSBpIHNhdmpldG92YW5qYS4gVSBuYcWhZW0gU2t5IE9mZmljZXUgem5hbmplIGRpamVsaW1v',
            'IG90dm9yZW5vLCBwb21hxb5lbW8gamVkbmkgZHJ1Z2ltYSBpIG96YmlsamFuIHBvc2FvIGdyYWRpbW8gdSBhdG1vc2ZlcmkgdSBrb2pvaiBsanVkaSBtb2d1',
            'IGJpdGkgc3ZvamkuPC9wPjxwPk9kIHBydm9nIGRhbmEgcmFkaXQgxIdlxaEgbmEgc3R2YXJuaW0gcG9zbG92bmltIGl6YXpvdmltYSwgdXogbWVudG9yYSwg',
            'cG9kcsWha3UgaXNrdXNuaWgga29sZWdhIGkgcHJvc3RvciBkYSBwcmVkbG/FvmnFoSBib2xqaSBuYcSNaW4gcmFkYS4gxb1lbGltbyBkYSBwb3NhbyBidWRl',
            'IG1qZXN0byBuYSBrb2plbSByYXN0ZcWhIGkga29qZW0gc2UgcmFkbyB2cmHEh2HFoS48L3A+In0sInZhbHVlcyI6WyJwb3ZqZXJlbmplIiwicG9kcsWha3Ui',
            'LCJwcmlsaWt1IHphIHJhenZvaiIsInByb3N0b3IgemEgaWRlamUiLCJ0aW0ga29qaSBpaCBndXJhIG5hcHJpamVkIl0sInByb2Nlc3MiOnsiaW50cm8iOiJW',
            'amVydWplbW8gZGEgc2UgcG90ZW5jaWphbCByYXp2aWphIGtyb3ogaXNrdXN0dm8sIG1lbnRvcnN0dm8gaSBwcmlsaWtlLiBaYXRvIG5hxaFpIHphcG9zbGVu',
            'aWNpIG9kIHBydm9nIGRhbmEgYWt0aXZubyBzdWRqZWx1anUgdSBwcm9qZWt0aW1hLCBzdXJhxJF1anUgcyBrbGlqZW50aW1hIGkgcmF6dmlqYWp1IHN0cnXE',
            'jW5hIHpuYW5qYSBrcm96IHJhZCBzIHJhemxpxI1pdGltIGluZHVzdHJpamFtYSBpIHBvc2xvdm5pbSBpemF6b3ZpbWEuIiwic3RlcHMiOlt7InN0ZXAiOiIw',
            'MSIsInRpdGxlIjoiUG92amVyZW5qZSIsImRlc2NyaXB0aW9uIjoiT2QgcHJ2b2cgZGFuYSBkb2JpdmHFoSBwcm9zdG9yIHN1ZGplbG92YXRpIHUgc3R2YXJu',
            'aW0gcHJvamVrdGltYSBpIHByZXV6aW1hdGkgb2Rnb3Zvcm5vc3QgdXogamFzbnUgcG9kcsWha3UgdGltYS4ifSx7InN0ZXAiOiIwMiIsInRpdGxlIjoiUG9k',
            'csWha2EiLCJkZXNjcmlwdGlvbiI6IlXEjWnFoSBrcm96IG1lbnRvcnN0dm8sIHN1cmFkbmp1IHMgaXNrdXNuaW0gc3RydcSNbmphY2ltYSBpIG90dm9yZW5v',
            'IGRpamVsamVuamUgem5hbmphIHVudXRhciB0aW1hLiJ9LHsic3RlcCI6IjAzIiwidGl0bGUiOiJQcmlsaWthIHphIHJhenZvaiIsImRlc2NyaXB0aW9uIjoi',
            'UmFkacWhIHMgcmF6bGnEjWl0aW0gaW5kdXN0cmlqYW1hIGkgcG9zbG92bmltIGl6YXpvdmltYSwgYmV6IMSNZWthbmphIGdvZGluYW1hIGRhIHBva2HFvmXF',
            'oSDFoXRvIHpuYcWhLiJ9LHsic3RlcCI6IjA0IiwidGl0bGUiOiJQcm9zdG9yIHphIGlkZWplIiwiZGVzY3JpcHRpb24iOiJDaWplbmltbyBwcm9ha3Rpdm5v',
            'c3QsIG5vdmEgcmplxaFlbmphIGkgbGp1ZGUga29qaSDFvmVsZSBha3Rpdm5vIGdyYWRpdGkgYm9samkgbmHEjWluIHJhZGEuIn1dLCJ0aXRsZSI6IlJhenZv',
            'aiBrb2ppIG5pamUgc2FtbyBmcmF6YSIsImtpY2tlciI6IlphxaF0byBBTFBIQSBDQVBJVEFMSVM/IiwidGl0bGVfbGluZV9vbmUiOiJSYXp2b2oga29qaSBu',
            'aWplIiwidGl0bGVfbGluZV90d28iOiJzYW1vIGZyYXphIn0sInN0b3JpZXMiOlt7Imxpc3QiOltdLCJ0aXRsZSI6IkxqdWRpIHpib2cga29qaWggb3N0YWpl',
            'xaEiLCJraWNrZXIiOiJUaW0iLCJib2R5X2h0bWwiOiI8cD5Nb8W+ZcWhIGltYXRpIG9kbGnEjWFuIHBvc2FvLCBhbGkgYmV6IGRvYnJvZyB0aW1hIG5pxaF0',
            'YSBuZW1hIHNtaXNsYS48L3A+PHA+VSBBTFBIQSBDQVBJVEFMSVNVIGdyYWRpbW8ga3VsdHVydSBtZcSRdXNvYm5vZyBwb8WhdG92YW5qYSwgc3VyYWRuamUg',
            'aSBvdHZvcmVuZSBrb211bmlrYWNpamUuIFZqZXJ1amVtbyB1IGRpamVsamVuamUgem5hbmphLCBwb2RyxaFrdSBtZcSRdSBrb2xlZ2FtYSBpIGF0bW9zZmVy',
            'dSB1IGtvam9qIGxqdWRpIG1vZ3UgYml0aSBwcm9mZXNpb25hbG5pLCBhbGkgaSBzdm9qaS48L3A+PHA+T3piaWxqbmkgc21vIHUgcG9zbHUsIGFsaSB2amVy',
            'dWplbW8gZGEgZG9icmEgYXRtb3NmZXJhIGkga3ZhbGl0ZXRuaSBvZG5vc2kgxI1pbmUgdmVsaWt1IHJhemxpa3UuPC9wPiIsImxpc3RfdGV4dCI6IiIsInBh',
            'cmFncmFwaHMiOlsiTW/FvmXFoSBpbWF0aSBvZGxpxI1hbiBwb3NhbywgYWxpIGJleiBkb2Jyb2cgdGltYSBuacWhdGEgbmVtYSBzbWlzbGEuIiwiVSBBTFBI',
            'QSBDQVBJVEFMSVNVIGdyYWRpbW8ga3VsdHVydSBtZcSRdXNvYm5vZyBwb8WhdG92YW5qYSwgc3VyYWRuamUgaSBvdHZvcmVuZSBrb211bmlrYWNpamUuIFZq',
            'ZXJ1amVtbyB1IGRpamVsamVuamUgem5hbmphLCBwb2RyxaFrdSBtZcSRdSBrb2xlZ2FtYSBpIGF0bW9zZmVydSB1IGtvam9qIGxqdWRpIG1vZ3UgYml0aSBw',
            'cm9mZXNpb25hbG5pLCBhbGkgaSBzdm9qaS4iLCJPemJpbGpuaSBzbW8gdSBwb3NsdSwgYWxpIHZqZXJ1amVtbyBkYSBkb2JyYSBhdG1vc2ZlcmEgaSBrdmFs',
            'aXRldG5pIG9kbm9zaSDEjWluZSB2ZWxpa3UgcmF6bGlrdS4iXX0seyJsaXN0IjpbXSwidGl0bGUiOiJPa3J1xb5lbmplIGtvamUgdGUgcG90acSNZSBuYSB2',
            'acWhZSIsImtpY2tlciI6Ikl6YXpvdmkiLCJib2R5X2h0bWwiOiI8cD5SYWRpbW8gcyBwb2R1emV0bmljaW1hLCBvYml0ZWxqc2tpbSB0dnJ0a2FtYSBpIGtv',
            'bXBhbmlqYW1hIGtvamUgcmFzdHUgaSByYXp2aWphanUgc2UuIFphdG8gbmkgbmHFoSBwb3NhbyBuaWplIHJ1dGluc2tpLjwvcD48cD5TdmFraSBwcm9qZWt0',
            'IGRvbm9zaSBub3ZlIGl6YXpvdmUsIG5vdmEgem5hbmphIGkgcHJpbGlrdSBkYSByYXp2aWphxaEgxaFpcnUgcG9zbG92bnUgcGVyc3Bla3RpdnUuPC9wPjxw',
            'PkFrbyB2b2xpxaEgZGluYW1pa3UsIG9kZ292b3Jub3N0IGkga29udGludWlyYW5pIHJhenZvaiAtIG9zamXEh2F0IMSHZcWhIHNlIGthbyBkb21hLjwvcD4i',
            'LCJsaXN0X3RleHQiOiIiLCJwYXJhZ3JhcGhzIjpbIlJhZGltbyBzIHBvZHV6ZXRuaWNpbWEsIG9iaXRlbGpza2ltIHR2cnRrYW1hIGkga29tcGFuaWphbWEg',
            'a29qZSByYXN0dSBpIHJhenZpamFqdSBzZS4gWmF0byBuaSBuYcWhIHBvc2FvIG5pamUgcnV0aW5za2kuIiwiU3Zha2kgcHJvamVrdCBkb25vc2kgbm92ZSBp',
            'emF6b3ZlLCBub3ZhIHpuYW5qYSBpIHByaWxpa3UgZGEgcmF6dmlqYcWhIMWhaXJ1IHBvc2xvdm51IHBlcnNwZWt0aXZ1LiIsIkFrbyB2b2xpxaEgZGluYW1p',
            'a3UsIG9kZ292b3Jub3N0IGkga29udGludWlyYW5pIHJhenZvaiAtIG9zamXEh2F0IMSHZcWhIHNlIGthbyBkb21hLiJdfSx7Imxpc3QiOlsiem5hbmplIiwi',
            'aXNrdXN0dm8iLCJvZG5vc2UiLCJzYW1vc3RhbG5vc3QiLCJrYXJpamVydSJdLCJ0aXRsZSI6IlJhc3RlbW8gemFqZWRubyIsImtpY2tlciI6IlJhc3QiLCJi',
            'b2R5X2h0bWwiOiI8cD5BTFBIQSBDQVBJVEFMSVMgbmlqZSBtamVzdG8gZ2RqZSBzYW1vIGRvbGF6acWhIG9kcmFkaXRpIHBvc2FvLjwvcD48cD5OYcWhIGNp',
            'bGogamUgc3R2b3JpdGkgb2tydcW+ZW5qZSB1IGtvamVtIGxqdWRpIGR1Z29yb8SNbm8gxb5lbGUgb3N0YXRpLCByYXp2aWphdGkgc2UgaSBiaXRpIHBvbm9z',
            'bmkgbmEgb25vIMWhdG8gemFqZWRubyBncmFkaW1vLjwvcD4iLCJsaXN0X3RleHQiOiJ6bmFuamVcbmlza3VzdHZvXG5vZG5vc2VcbnNhbW9zdGFsbm9zdFxu',
            'a2FyaWplcnUiLCJwYXJhZ3JhcGhzIjpbIkFMUEhBIENBUElUQUxJUyBuaWplIG1qZXN0byBnZGplIHNhbW8gZG9sYXppxaEgb2RyYWRpdGkgcG9zYW8uIiwi',
            'TmHFoSBjaWxqIGplIHN0dm9yaXRpIG9rcnXFvmVuamUgdSBrb2plbSBsanVkaSBkdWdvcm/EjW5vIMW+ZWxlIG9zdGF0aSwgcmF6dmlqYXRpIHNlIGkgYml0',
            'aSBwb25vc25pIG5hIG9ubyDFoXRvIHphamVkbm8gZ3JhZGltby4iXX1dLCJhcHBsaWNhdGlvbiI6eyJ0aXRsZSI6Ik90dm9yZW5lIHBvemljaWplIiwia2lj',
            'a2VyIjoiUHJpamF2ZSIsImJvZHlfaHRtbCI6IjxwPlRyYcW+aW1vIGFtYmljaW96bmUsIG9kZ292b3JuZSBpIHByb2FrdGl2bmUgbGp1ZGUga29qaSDFvmVs',
            'ZSByYXp2aWphdGkgc3ZvamUgem5hbmplIGkga2FyaWplcnUgdSBva3J1xb5lbmp1IGtvamUgcG90acSNZSByYXN0LjwvcD48cD5OZSB2aWRpxaEgb3R2b3Jl',
            'bnUgcG96aWNpanU/PC9wPjxwPlV2aWplayBzbW8gb3R2b3JlbmkgemEga3ZhbGl0ZXRuZSBsanVkZS4gQWtvIHZqZXJ1amXFoSBkYSBiaSBiaW8gZG9iYXIg',
            'ZGlvIEFMUEhBIENBUElUQUxJUyB0aW1hLCBwb8WhYWxqaSBuYW0gc3ZvaiDFvml2b3RvcGlzIGkgcHJlZHN0YXZpIHNlLiBNb8W+ZGEgdXByYXZvIHRpIGJ1',
            'ZGXFoSBuYcWhZSBzbGplZGXEh2UgdmVsaWtvIHBvamHEjWFuamUuPC9wPiIsImhpZ2hsaWdodCI6IlByb25hxJFpIHN2b2plIG1qZXN0byB1IG5hxaFlbSB0',
            'aW11IiwicGFyYWdyYXBocyI6WyJUcmHFvmltbyBhbWJpY2lvem5lLCBvZGdvdm9ybmUgaSBwcm9ha3Rpdm5lIGxqdWRlIGtvamkgxb5lbGUgcmF6dmlqYXRp',
            'IHN2b2plIHpuYW5qZSBpIGthcmlqZXJ1IHUgb2tydcW+ZW5qdSBrb2plIHBvdGnEjWUgcmFzdC4iLCJOZSB2aWRpxaEgb3R2b3JlbnUgcG96aWNpanU/Iiwi',
            'VXZpamVrIHNtbyBvdHZvcmVuaSB6YSBrdmFsaXRldG5lIGxqdWRlLiBBa28gdmplcnVqZcWhIGRhIGJpIGJpbyBkb2JhciBkaW8gQUxQSEEgQ0FQSVRBTElT',
            'IHRpbWEsIHBvxaFhbGppIG5hbSBzdm9qIMW+aXZvdG9waXMgaSBwcmVkc3Rhdmkgc2UuIE1vxb5kYSB1cHJhdm8gdGkgYnVkZcWhIG5hxaFlIHNsamVkZcSH',
            'ZSB2ZWxpa28gcG9qYcSNYW5qZS4iXX0sInZhbHVlc190ZXh0IjoicG92amVyZW5qZVxucG9kcsWha3VcbnByaWxpa3UgemEgcmF6dm9qXG5wcm9zdG9yIHph',
            'IGlkZWplXG50aW0ga29qaSBpaCBndXJhIG5hcHJpamVkIiwic3Rvcmllc19zZWN0aW9uIjp7ImludHJvIjoiVmnFoWUgb2QgcmFkbm9nIG1qZXN0YSIsInRp',
            'dGxlIjoixb1pdm90IHUgQUxQSEEgQ0FQSVRBTElTVSJ9fQ=='
        )
    ) USING utf8mb4), '$')
    ),
    updated_at = NOW()
WHERE page_id = @ac_career_page_id
  AND locale = 'hr';

UPDATE content_info_page_translations
SET
    payload = JSON_SET(
        COALESCE(payload, JSON_OBJECT()),
        '$.career_page',
        JSON_EXTRACT(CONVERT(FROM_BASE64(
        CONCAT(
            'eyJmb3JtIjp7ImN2IjoiQ1YgdXBsb2FkIiwiZW1haWwiOiJFbWFpbCIsImludHJvIjoiRmlsbCBpbiB5b3VyIGJhc2ljIGRldGFpbHMgYW5kIHVwbG9hZCB5',
            'b3VyIHJlc3VtZSBzbyB3ZSBjYW4gY29udGFjdCB5b3Ugd2hlbiB5b3VyIHByb2ZpbGUgbWF0Y2hlcyBhbiBvcGVuIHBvc2l0aW9uLiIsInRpdGxlIjoiU2Vu',
            'ZCB1cyB5b3VyIENWIiwic3VibWl0IjoiU2VuZCBhcHBsaWNhdGlvbiIsImN2X2hlbHAiOiJTdXBwb3J0ZWQgZm9ybWF0czogUERGLCBET0MsIGFuZCBET0NY',
            'LiBNYXhpbXVtIGZpbGUgc2l6ZSBpcyA1IE1CLiIsIm1lc3NhZ2UiOiJNZXNzYWdlIChvcHRpb25hbCkiLCJjdl9lbXB0eSI6Ik5vIGZpbGUgc2VsZWN0ZWQu',
            'IiwiY3ZfYnV0dG9uIjoiQ2hvb3NlIGZpbGUiLCJsYXN0X25hbWUiOiJMYXN0IG5hbWUiLCJmaXJzdF9uYW1lIjoiRmlyc3QgbmFtZSIsImFjY2VwdF90ZXJt',
            'cyI6IkkgYWdyZWUgdG8gdGhlIHByb2Nlc3Npbmcgb2YgcGVyc29uYWwgZGF0YSBmb3IgcmVjcnVpdG1lbnQgcHVycG9zZXMuIn0sImludHJvIjp7ImJvZHki',
            'OlsiV2UgYXJlIGxvb2tpbmcgZm9yIHBlb3BsZSB3aG8gd2FudCB0byBsZWFybiwgZGV2ZWxvcCwgdGFrZSByZXNwb25zaWJpbGl0eSBhbmQgYnVpbGQgc29t',
            'ZXRoaW5nIGxvbmctdGVybSB3aXRoIHVzLiIsIlRvZGF5LCBBTFBIQSBDQVBJVEFMSVMgYnJpbmdzIHRvZ2V0aGVyIDc1IGV4cGVydHMgaW4gYWNjb3VudGlu',
            'ZywgZmluYW5jZSwgYXVkaXQsIEVVIGZ1bmRzIGFuZCBhZHZpc29yeS4gSW4gb3VyIFNreSBPZmZpY2UsIHdlIHNoYXJlIGtub3dsZWRnZSBvcGVubHksIGhl',
            'bHAgb25lIGFub3RoZXIgYW5kIGJ1aWxkIHNlcmlvdXMgd29yayBpbiBhbiBhdG1vc3BoZXJlIHdoZXJlIHBlb3BsZSBjYW4gYmUgdGhlbXNlbHZlcy4iLCJX',
            'aXRoIHVzLCB5b3Ugd2lsbCB3b3JrIG9uIHJlYWwgYnVzaW5lc3MgY2hhbGxlbmdlcywgY29sbGFib3JhdGUgd2l0aCBleHBlcmllbmNlZCBwcm9mZXNzaW9u',
            'YWxzIGFuZCBoYXZlIHRoZSBvcHBvcnR1bml0eSB0byBncm93IGZhc3RlciB0aGFuIGluIGEgY2xhc3NpYyBjb3Jwb3JhdGUgZW52aXJvbm1lbnQuIl0sInRp',
            'dGxlIjoiQSBwbGFjZSB3aGVyZSBwZW9wbGUgYW5kIGNhcmVlcnMgZ3JvdyIsImtpY2tlciI6Ikdyb3dpbmcgdG9nZXRoZXIiLCJoaWdobGlnaHQiOiJXZSBh',
            'cmUgbG9va2luZyBmb3IgcGVvcGxlLCBub3QganVzdCByZXN1bWVzLiIsImltYWdlX2FsdCI6IiIsInN0YXRfbGFiZWwiOiJleHBlcnRzIGluIGFjY291bnRp',
            'bmcsIGZpbmFuY2UsIGF1ZGl0IGFuZCBhZHZpc29yeSIsInN0YXRfdmFsdWUiOiI3NSIsImJ1dHRvbl9sYWJlbCI6Ik9QRU4gUE9TSVRJT05TIiwidmFsdWVz',
            'X2xhYmVsIjoiV2hhdCB3ZSBvZmZlciIsInNlY3Rpb25fdGl0bGUiOiJBIGNhcmVlciBhdCBBTFBIQSBDQVBJVEFMSVMiLCJoZXJvX2JvZHlfaHRtbCI6Ijxw',
            'PlRvZGF5LCBBTFBIQSBDQVBJVEFMSVMgYnJpbmdzIHRvZ2V0aGVyIDc1IGV4cGVydHMgaW4gYWNjb3VudGluZywgZmluYW5jZSwgYXVkaXQsIEVVIGZ1bmRz',
            'IGFuZCBhZHZpc29yeS4gSW4gb3VyIFNreSBPZmZpY2UsIHdlIHNoYXJlIGtub3dsZWRnZSBvcGVubHksIGhlbHAgb25lIGFub3RoZXIgYW5kIGJ1aWxkIHNl',
            'cmlvdXMgd29yayBpbiBhbiBhdG1vc3BoZXJlIHdoZXJlIHBlb3BsZSBjYW4gYmUgdGhlbXNlbHZlcy48L3A+PHA+V2l0aCB1cywgeW91IHdpbGwgd29yayBv',
            'biByZWFsIGJ1c2luZXNzIGNoYWxsZW5nZXMsIGNvbGxhYm9yYXRlIHdpdGggZXhwZXJpZW5jZWQgcHJvZmVzc2lvbmFscyBhbmQgaGF2ZSB0aGUgb3Bwb3J0',
            'dW5pdHkgdG8gZ3JvdyBmYXN0ZXIgdGhhbiBpbiBhIGNsYXNzaWMgY29ycG9yYXRlIGVudmlyb25tZW50LjwvcD4ifSwidmFsdWVzIjpbInRydXN0Iiwic3Vw',
            'cG9ydCIsImRldmVsb3BtZW50IG9wcG9ydHVuaXRpZXMiLCJyb29tIGZvciBpZGVhcyIsImEgdGVhbSB0aGF0IHB1c2hlcyB0aGVtIGZvcndhcmQiXSwicHJv',
            'Y2VzcyI6eyJpbnRybyI6IldlIGJlbGlldmUgcG90ZW50aWFsIGdyb3dzIHRocm91Z2ggZXhwZXJpZW5jZSwgbWVudG9yc2hpcCBhbmQgb3Bwb3J0dW5pdHku',
            'IEZyb20gZGF5IG9uZSwgb3VyIHBlb3BsZSBhY3RpdmVseSBwYXJ0aWNpcGF0ZSBpbiBwcm9qZWN0cywgY29sbGFib3JhdGUgd2l0aCBjbGllbnRzIGFuZCBi',
            'dWlsZCBleHBlcnRpc2UgdGhyb3VnaCB3b3JrIGFjcm9zcyBpbmR1c3RyaWVzIGFuZCBidXNpbmVzcyBjaGFsbGVuZ2VzLiIsInN0ZXBzIjpbeyJzdGVwIjoi',
            'MDEiLCJ0aXRsZSI6IlRydXN0IiwiZGVzY3JpcHRpb24iOiJGcm9tIGRheSBvbmUsIHlvdSBnZXQgdGhlIHNwYWNlIHRvIHBhcnRpY2lwYXRlIGluIHJlYWwg',
            'cHJvamVjdHMgYW5kIHRha2UgcmVzcG9uc2liaWxpdHkgd2l0aCBjbGVhciB0ZWFtIHN1cHBvcnQuIn0seyJzdGVwIjoiMDIiLCJ0aXRsZSI6IlN1cHBvcnQi',
            'LCJkZXNjcmlwdGlvbiI6IllvdSBsZWFybiB0aHJvdWdoIG1lbnRvcnNoaXAsIGNvbGxhYm9yYXRpb24gd2l0aCBleHBlcmllbmNlZCBwcm9mZXNzaW9uYWxz',
            'IGFuZCBvcGVuIGtub3dsZWRnZSBzaGFyaW5nLiJ9LHsic3RlcCI6IjAzIiwidGl0bGUiOiJHcm93dGggb3Bwb3J0dW5pdHkiLCJkZXNjcmlwdGlvbiI6Illv',
            'dSB3b3JrIHdpdGggZGlmZmVyZW50IGluZHVzdHJpZXMgYW5kIGJ1c2luZXNzIGNoYWxsZW5nZXMgd2l0aG91dCB3YWl0aW5nIHllYXJzIHRvIHNob3cgd2hh',
            'dCB5b3UgY2FuIGRvLiJ9LHsic3RlcCI6IjA0IiwidGl0bGUiOiJSb29tIGZvciBpZGVhcyIsImRlc2NyaXB0aW9uIjoiV2UgdmFsdWUgcHJvYWN0aXZpdHks',
            'IG5ldyBzb2x1dGlvbnMgYW5kIHBlb3BsZSB3aG8gd2FudCB0byBhY3RpdmVseSBidWlsZCBiZXR0ZXIgd2F5cyBvZiB3b3JraW5nLiJ9XSwidGl0bGUiOiJE',
            'ZXZlbG9wbWVudCB0aGF0IGlzIG1vcmUgdGhhbiBhIHBocmFzZSIsImtpY2tlciI6IldoeSBBTFBIQSBDQVBJVEFMSVM/IiwidGl0bGVfbGluZV9vbmUiOiJE',
            'ZXZlbG9wbWVudCB0aGF0IGlzIiwidGl0bGVfbGluZV90d28iOiJtb3JlIHRoYW4gYSBwaHJhc2UifSwic3RvcmllcyI6W3sibGlzdCI6W10sInRpdGxlIjoi',
            'UGVvcGxlIHdobyBtYWtlIHlvdSBzdGF5Iiwia2lja2VyIjoiVGVhbSIsImJvZHlfaHRtbCI6IjxwPllvdSBjYW4gaGF2ZSBhIGdyZWF0IGpvYiwgYnV0IHdp',
            'dGhvdXQgYSBnb29kIHRlYW0gaXQgZG9lcyBub3QgbWVhbiBtdWNoLjwvcD48cD5BdCBBTFBIQSBDQVBJVEFMSVMsIHdlIGJ1aWxkIGEgY3VsdHVyZSBvZiBt',
            'dXR1YWwgcmVzcGVjdCwgY29sbGFib3JhdGlvbiBhbmQgb3BlbiBjb21tdW5pY2F0aW9uLiBXZSBiZWxpZXZlIGluIGtub3dsZWRnZSBzaGFyaW5nLCBzdXBw',
            'b3J0IGFtb25nIGNvbGxlYWd1ZXMgYW5kIGFuIGF0bW9zcGhlcmUgd2hlcmUgcGVvcGxlIGNhbiBiZSBwcm9mZXNzaW9uYWwgYW5kIHN0aWxsIGJlIHRoZW1z',
            'ZWx2ZXMuPC9wPjxwPldlIGFyZSBzZXJpb3VzIGFib3V0IHdvcmssIGJ1dCB3ZSBiZWxpZXZlIGEgZ29vZCBhdG1vc3BoZXJlIGFuZCBxdWFsaXR5IHJlbGF0',
            'aW9uc2hpcHMgbWFrZSBhIHJlYWwgZGlmZmVyZW5jZS48L3A+IiwibGlzdF90ZXh0IjoiIiwicGFyYWdyYXBocyI6WyJZb3UgY2FuIGhhdmUgYSBncmVhdCBq',
            'b2IsIGJ1dCB3aXRob3V0IGEgZ29vZCB0ZWFtIGl0IGRvZXMgbm90IG1lYW4gbXVjaC4iLCJBdCBBTFBIQSBDQVBJVEFMSVMsIHdlIGJ1aWxkIGEgY3VsdHVy',
            'ZSBvZiBtdXR1YWwgcmVzcGVjdCwgY29sbGFib3JhdGlvbiBhbmQgb3BlbiBjb21tdW5pY2F0aW9uLiBXZSBiZWxpZXZlIGluIGtub3dsZWRnZSBzaGFyaW5n',
            'LCBzdXBwb3J0IGFtb25nIGNvbGxlYWd1ZXMgYW5kIGFuIGF0bW9zcGhlcmUgd2hlcmUgcGVvcGxlIGNhbiBiZSBwcm9mZXNzaW9uYWwgYW5kIHN0aWxsIGJl',
            'IHRoZW1zZWx2ZXMuIiwiV2UgYXJlIHNlcmlvdXMgYWJvdXQgd29yaywgYnV0IHdlIGJlbGlldmUgYSBnb29kIGF0bW9zcGhlcmUgYW5kIHF1YWxpdHkgcmVs',
            'YXRpb25zaGlwcyBtYWtlIGEgcmVhbCBkaWZmZXJlbmNlLiJdfSx7Imxpc3QiOltdLCJ0aXRsZSI6IkFuIGVudmlyb25tZW50IHRoYXQgcHVzaGVzIHlvdSBm',
            'dXJ0aGVyIiwia2lja2VyIjoiQ2hhbGxlbmdlcyIsImJvZHlfaHRtbCI6IjxwPldlIHdvcmsgd2l0aCBlbnRyZXByZW5ldXJzLCBmYW1pbHkgYnVzaW5lc3Nl',
            'cyBhbmQgZ3Jvd2luZyBjb21wYW5pZXMuIFRoYXQgaXMgd2h5IG91ciB3b3JrIGlzIG5vdCByb3V0aW5lLjwvcD48cD5FdmVyeSBwcm9qZWN0IGJyaW5ncyBu',
            'ZXcgY2hhbGxlbmdlcywgbmV3IGtub3dsZWRnZSBhbmQgYW4gb3Bwb3J0dW5pdHkgdG8gZGV2ZWxvcCBhIGJyb2FkZXIgYnVzaW5lc3MgcGVyc3BlY3RpdmUu',
            'PC9wPjxwPklmIHlvdSBsaWtlIGR5bmFtaWNzLCByZXNwb25zaWJpbGl0eSBhbmQgY29udGludW91cyBkZXZlbG9wbWVudCwgeW91IHdpbGwgZmVlbCBhdCBo',
            'b21lLjwvcD4iLCJsaXN0X3RleHQiOiIiLCJwYXJhZ3JhcGhzIjpbIldlIHdvcmsgd2l0aCBlbnRyZXByZW5ldXJzLCBmYW1pbHkgYnVzaW5lc3NlcyBhbmQg',
            'Z3Jvd2luZyBjb21wYW5pZXMuIFRoYXQgaXMgd2h5IG91ciB3b3JrIGlzIG5vdCByb3V0aW5lLiIsIkV2ZXJ5IHByb2plY3QgYnJpbmdzIG5ldyBjaGFsbGVu',
            'Z2VzLCBuZXcga25vd2xlZGdlIGFuZCBhbiBvcHBvcnR1bml0eSB0byBkZXZlbG9wIGEgYnJvYWRlciBidXNpbmVzcyBwZXJzcGVjdGl2ZS4iLCJJZiB5b3Ug',
            'bGlrZSBkeW5hbWljcywgcmVzcG9uc2liaWxpdHkgYW5kIGNvbnRpbnVvdXMgZGV2ZWxvcG1lbnQsIHlvdSB3aWxsIGZlZWwgYXQgaG9tZS4iXX0seyJsaXN0',
            'IjpbImtub3dsZWRnZSIsImV4cGVyaWVuY2UiLCJyZWxhdGlvbnNoaXBzIiwiaW5kZXBlbmRlbmNlIiwiY2FyZWVyIl0sInRpdGxlIjoiV2UgZ3JvdyB0b2dl',
            'dGhlciIsImtpY2tlciI6Ikdyb3d0aCIsImJvZHlfaHRtbCI6IjxwPkFMUEhBIENBUElUQUxJUyBpcyBub3QgYSBwbGFjZSB3aGVyZSB5b3Ugb25seSBjb21l',
            'IHRvIGZpbmlzaCB0YXNrcy48L3A+PHA+T3VyIGdvYWwgaXMgdG8gY3JlYXRlIGFuIGVudmlyb25tZW50IHdoZXJlIHBlb3BsZSB3YW50IHRvIHN0YXkgbG9u',
            'Zy10ZXJtLCBkZXZlbG9wIGFuZCBiZSBwcm91ZCBvZiB3aGF0IHdlIGJ1aWxkIHRvZ2V0aGVyLjwvcD4iLCJsaXN0X3RleHQiOiJrbm93bGVkZ2VcbmV4cGVy',
            'aWVuY2VcbnJlbGF0aW9uc2hpcHNcbmluZGVwZW5kZW5jZVxuY2FyZWVyIiwicGFyYWdyYXBocyI6WyJBTFBIQSBDQVBJVEFMSVMgaXMgbm90IGEgcGxhY2Ug',
            'd2hlcmUgeW91IG9ubHkgY29tZSB0byBmaW5pc2ggdGFza3MuIiwiT3VyIGdvYWwgaXMgdG8gY3JlYXRlIGFuIGVudmlyb25tZW50IHdoZXJlIHBlb3BsZSB3',
            'YW50IHRvIHN0YXkgbG9uZy10ZXJtLCBkZXZlbG9wIGFuZCBiZSBwcm91ZCBvZiB3aGF0IHdlIGJ1aWxkIHRvZ2V0aGVyLiJdfV0sImFwcGxpY2F0aW9uIjp7',
            'InRpdGxlIjoiT3BlbiBwb3NpdGlvbnMiLCJraWNrZXIiOiJBcHBsaWNhdGlvbnMiLCJib2R5X2h0bWwiOiI8cD5XZSBhcmUgbG9va2luZyBmb3IgYW1iaXRp',
            'b3VzLCByZXNwb25zaWJsZSBhbmQgcHJvYWN0aXZlIHBlb3BsZSB3aG8gd2FudCB0byBkZXZlbG9wIHRoZWlyIGtub3dsZWRnZSBhbmQgY2FyZWVyIGluIGFu',
            'IGVudmlyb25tZW50IHRoYXQgZW5jb3VyYWdlcyBncm93dGguPC9wPjxwPkRvIG5vdCBzZWUgYW4gb3BlbiBwb3NpdGlvbj88L3A+PHA+V2UgYXJlIGFsd2F5',
            'cyBvcGVuIHRvIHF1YWxpdHkgcGVvcGxlLiBJZiB5b3UgYmVsaWV2ZSB5b3Ugd291bGQgYmUgYSBnb29kIHBhcnQgb2YgdGhlIEFMUEhBIENBUElUQUxJUyB0',
            'ZWFtLCBzZW5kIHVzIHlvdXIgQ1YgYW5kIGludHJvZHVjZSB5b3Vyc2VsZi48L3A+IiwiaGlnaGxpZ2h0IjoiRmluZCB5b3VyIHBsYWNlIGluIG91ciB0ZWFt',
            'IiwicGFyYWdyYXBocyI6WyJXZSBhcmUgbG9va2luZyBmb3IgYW1iaXRpb3VzLCByZXNwb25zaWJsZSBhbmQgcHJvYWN0aXZlIHBlb3BsZSB3aG8gd2FudCB0',
            'byBkZXZlbG9wIHRoZWlyIGtub3dsZWRnZSBhbmQgY2FyZWVyIGluIGFuIGVudmlyb25tZW50IHRoYXQgZW5jb3VyYWdlcyBncm93dGguIiwiRG8gbm90IHNl',
            'ZSBhbiBvcGVuIHBvc2l0aW9uPyIsIldlIGFyZSBhbHdheXMgb3BlbiB0byBxdWFsaXR5IHBlb3BsZS4gSWYgeW91IGJlbGlldmUgeW91IHdvdWxkIGJlIGEg',
            'Z29vZCBwYXJ0IG9mIHRoZSBBTFBIQSBDQVBJVEFMSVMgdGVhbSwgc2VuZCB1cyB5b3VyIENWIGFuZCBpbnRyb2R1Y2UgeW91cnNlbGYuIl19LCJ2YWx1ZXNf',
            'dGV4dCI6InRydXN0XG5zdXBwb3J0XG5kZXZlbG9wbWVudCBvcHBvcnR1bml0aWVzXG5yb29tIGZvciBpZGVhc1xuYSB0ZWFtIHRoYXQgcHVzaGVzIHRoZW0g',
            'Zm9yd2FyZCIsInN0b3JpZXNfc2VjdGlvbiI6eyJpbnRybyI6Ik1vcmUgdGhhbiBhIHdvcmtwbGFjZSIsInRpdGxlIjoiTGlmZSBhdCBBTFBIQSBDQVBJVEFM',
            'SVMifX0='
        )
    ) USING utf8mb4), '$')
    ),
    updated_at = NOW()
WHERE page_id = @ac_career_page_id
  AND locale = 'en';

-- Team: Danijel's credential.
SET @ac_danijel_id := COALESCE(
    (
        SELECT id
        FROM content_team_members
        WHERE code = 'danijel-pevec'
        LIMIT 1
    ),
    (
        SELECT team_member_id
        FROM content_team_member_translations
        WHERE LOWER(name) LIKE '%danijel pevec%'
        LIMIT 1
    )
);

UPDATE content_team_member_translations
SET
    name = 'mr. Danijel Pevec, CFBA',
    updated_at = NOW()
WHERE team_member_id = @ac_danijel_id;

-- Team: update only Ana Mandić's Croatian biography if she already exists.
SET @ac_ana_id := COALESCE(
    (
        SELECT id
        FROM content_team_members
        WHERE code = 'ana-mandic'
        LIMIT 1
    ),
    (
        SELECT team_member_id
        FROM content_team_member_translations
        WHERE LOWER(name) LIKE '%ana mandi%'
        LIMIT 1
    )
);

UPDATE content_team_member_translations
SET
    description_html = '<p>Ana posjeduje ACCA kvalifikaciju i doktorat znanosti s Ekonomskog fakulteta u Zagrebu.</p><p>Ima više od dvanaest godina iskustva u globalnoj konzultantskoj tvrtki, radeći na projektima savjetovanja pri transakcijama, uključujući:</p><ul><li><strong>Transakcijske usluge:</strong> vođenje financijskih due diligence analiza u različitim industrijama za strateške i financijske investitore, pružanje podrške klijentima u HR due diligence angažmanima te izrada planova sinergije i planova razdvajanja poslovanja.</li><li><strong>Upravljanje financijskim i računovodstvenim aspektima transakcija:</strong> strukturiranje transakcije i priprema dokumentacije, uključujući SPA i ostale transakcijske dokumente.</li><li><strong>Procjene vrijednosti:</strong> iskustvo u procjenama vrijednosti primjenom prihodovne, tržišne i likvidacijske metode.</li><li><strong>Savjetovanje pri zaduživanju:</strong> pružanje sveobuhvatne podrške klijentima pri pribavljanju i strukturiranju financiranja transakcija.</li><li><strong>Spajanja i preuzimanja (M&amp;A):</strong> podrška kupcima i prodavateljima tijekom procesa spajanja i preuzimanja.</li><li><strong>Forenzika:</strong> vođenje dijela istrage u okviru forenzične računovodstvene istrage.</li><li><strong>Financijske usluge:</strong> vođenje due diligence procesa portfelja nenaplativih potraživanja.</li></ul>',
    updated_at = NOW()
WHERE team_member_id = @ac_ana_id
  AND locale = 'hr';

-- Editorial media linked to the versioned image files deployed with the application.
-- These records remain replaceable through the normal CMS upload fields. No team media
-- collection is selected, deleted or inserted anywhere in this section.
SET @ac_audit_page_id := (
    SELECT id
    FROM content_service_pages
    WHERE code = 'audit' OR template_key = 'audit'
    ORDER BY CASE WHEN code = 'audit' THEN 0 ELSE 1 END, id
    LIMIT 1
);

SET @ac_services_index_page_id := (
    SELECT id
    FROM content_service_pages
    WHERE code = 'services' OR template_key = 'services_index'
    ORDER BY CASE WHEN code = 'services' THEN 0 ELSE 1 END, id
    LIMIT 1
);

DELETE FROM media
WHERE model_type = 'App\\Models\\Content\\Page\\InfoPage'
  AND model_id = @ac_career_page_id
  AND collection_name IN ('career_hero_image', 'career_gallery_images');

INSERT INTO media (
    model_type, model_id, uuid, collection_name, name, file_name, mime_type,
    disk, conversions_disk, size, manipulations, custom_properties,
    generated_conversions, responsive_images, order_column, created_at, updated_at
)
SELECT
    'App\\Models\\Content\\Page\\InfoPage', @ac_career_page_id, UUID(),
    'career_hero_image', 'ALPHA CAPITALIS tim na team buildingu',
    'career-team-building.jpg', 'image/jpeg', 'public', 'public', 356500,
    JSON_OBJECT(),
    JSON_OBJECT(
        'bundled_asset_path', 'front-theme/images/career/career-team-building.jpg',
        'alt', JSON_OBJECT(
            'hr', 'ALPHA CAPITALIS tim na team buildingu',
            'en', 'ALPHA CAPITALIS team at a team-building gathering'
        )
    ),
    JSON_OBJECT(), JSON_OBJECT(), 1, NOW(), NOW()
FROM DUAL
WHERE @ac_career_page_id IS NOT NULL;

INSERT INTO media (
    model_type, model_id, uuid, collection_name, name, file_name, mime_type,
    disk, conversions_disk, size, manipulations, custom_properties,
    generated_conversions, responsive_images, order_column, created_at, updated_at
)
SELECT
    'App\\Models\\Content\\Page\\InfoPage', @ac_career_page_id, UUID(),
    'career_gallery_images', 'Detalj ureda ALPHA CAPITALISA',
    'career-office-detail.jpg', 'image/jpeg', 'public', 'public', 263152,
    JSON_OBJECT(),
    JSON_OBJECT(
        'bundled_asset_path', 'front-theme/images/career/career-office-detail.jpg',
        'alt', JSON_OBJECT(
            'hr', 'Brendirani prostor ureda ALPHA CAPITALISA',
            'en', 'A branded area in the ALPHA CAPITALIS office'
        )
    ),
    JSON_OBJECT(), JSON_OBJECT(), 1, NOW(), NOW()
FROM DUAL
WHERE @ac_career_page_id IS NOT NULL;

INSERT INTO media (
    model_type, model_id, uuid, collection_name, name, file_name, mime_type,
    disk, conversions_disk, size, manipulations, custom_properties,
    generated_conversions, responsive_images, order_column, created_at, updated_at
)
SELECT
    'App\\Models\\Content\\Page\\InfoPage', @ac_career_page_id, UUID(),
    'career_gallery_images', 'Radni prostor ALPHA CAPITALISA',
    'career-office.jpg', 'image/jpeg', 'public', 'public', 166589,
    JSON_OBJECT(),
    JSON_OBJECT(
        'bundled_asset_path', 'front-theme/images/career/career-office.jpg',
        'alt', JSON_OBJECT(
            'hr', 'ALPHA CAPITALIS tim u zajedničkom uredskom prostoru',
            'en', 'The ALPHA CAPITALIS team in a shared office space'
        )
    ),
    JSON_OBJECT(), JSON_OBJECT(), 2, NOW(), NOW()
FROM DUAL
WHERE @ac_career_page_id IS NOT NULL;

INSERT INTO media (
    model_type, model_id, uuid, collection_name, name, file_name, mime_type,
    disk, conversions_disk, size, manipulations, custom_properties,
    generated_conversions, responsive_images, order_column, created_at, updated_at
)
SELECT
    'App\\Models\\Content\\Page\\InfoPage', @ac_career_page_id, UUID(),
    'career_gallery_images', 'Suradnja ALPHA CAPITALIS stručnjaka',
    'career-team-collaboration.jpg', 'image/jpeg', 'public', 'public', 109524,
    JSON_OBJECT(),
    JSON_OBJECT(
        'bundled_asset_path', 'front-theme/images/career/career-team-collaboration.jpg',
        'alt', JSON_OBJECT(
            'hr', 'Dvojica ALPHA CAPITALIS stručnjaka tijekom zajedničkog rada',
            'en', 'Two ALPHA CAPITALIS specialists working together'
        )
    ),
    JSON_OBJECT(), JSON_OBJECT(), 3, NOW(), NOW()
FROM DUAL
WHERE @ac_career_page_id IS NOT NULL;

DELETE FROM media
WHERE model_type = 'App\\Models\\Content\\Service\\ServicePage'
  AND (
      (model_id = @ac_audit_page_id AND collection_name = 'service_hero_image')
      OR
      (model_id = @ac_services_index_page_id AND collection_name = 'services_index_audit_image')
  );

INSERT INTO media (
    model_type, model_id, uuid, collection_name, name, file_name, mime_type,
    disk, conversions_disk, size, manipulations, custom_properties,
    generated_conversions, responsive_images, order_column, created_at, updated_at
)
SELECT
    'App\\Models\\Content\\Service\\ServicePage', @ac_audit_page_id, UUID(),
    'service_hero_image', 'Predaja ALPHA CAPITALIS vizitke na poslovnom sastanku',
    'audit-client-meeting.jpg', 'image/jpeg', 'public', 'public', 89548,
    JSON_OBJECT(),
    JSON_OBJECT(
        'bundled_asset_path', 'front-theme/images/services/audit-client-meeting.jpg',
        'alt', JSON_OBJECT(
            'hr', 'Predaja ALPHA CAPITALIS vizitke na poslovnom sastanku',
            'en', 'An ALPHA CAPITALIS business card being handed over at a client meeting'
        )
    ),
    JSON_OBJECT(), JSON_OBJECT(), 1, NOW(), NOW()
FROM DUAL
WHERE @ac_audit_page_id IS NOT NULL;

INSERT INTO media (
    model_type, model_id, uuid, collection_name, name, file_name, mime_type,
    disk, conversions_disk, size, manipulations, custom_properties,
    generated_conversions, responsive_images, order_column, created_at, updated_at
)
SELECT
    'App\\Models\\Content\\Service\\ServicePage', @ac_services_index_page_id, UUID(),
    'services_index_audit_image', 'Predaja ALPHA CAPITALIS vizitke na poslovnom sastanku',
    'audit-client-meeting.jpg', 'image/jpeg', 'public', 'public', 89548,
    JSON_OBJECT(),
    JSON_OBJECT(
        'bundled_asset_path', 'front-theme/images/services/audit-client-meeting.jpg',
        'alt', JSON_OBJECT(
            'hr', 'Predaja ALPHA CAPITALIS vizitke na poslovnom sastanku',
            'en', 'An ALPHA CAPITALIS business card being handed over at a client meeting'
        )
    ),
    JSON_OBJECT(), JSON_OBJECT(), 1, NOW(), NOW()
FROM DUAL
WHERE @ac_services_index_page_id IS NOT NULL;

COMMIT;

-- Verification: updated CMS content.
SELECT
    block.code,
    translation.locale,
    translation.title,
    translation.subtitle,
    JSON_UNQUOTE(JSON_EXTRACT(translation.payload, '$.stats[1].value')) AS clients,
    JSON_UNQUOTE(JSON_EXTRACT(translation.payload, '$.stats[2].value')) AS experts
FROM content_blocks AS block
INNER JOIN content_block_translations AS translation
    ON translation.content_block_id = block.id
WHERE block.code IN ('home-alpha-hero', 'home-alpha-stats')
ORDER BY block.code, translation.locale;

SELECT
    page.code,
    translation.locale,
    JSON_UNQUOTE(JSON_EXTRACT(translation.payload, '$.about_page.hero.stat_value')) AS about_clients,
    JSON_UNQUOTE(JSON_EXTRACT(translation.payload, '$.career_page.hero.stat_value')) AS career_experts
FROM content_info_pages AS page
INNER JOIN content_info_page_translations AS translation
    ON translation.page_id = page.id
WHERE page.id IN (@ac_about_page_id, @ac_career_page_id)
ORDER BY page.code, translation.locale;

SELECT
    member.code,
    member.sort_order,
    translation.locale,
    translation.name,
    translation.position
FROM content_team_members AS member
LEFT JOIN content_team_member_translations AS translation
    ON translation.team_member_id = member.id
WHERE member.code IN ('danijel-pevec', 'ana-mandic')
ORDER BY member.sort_order, translation.locale;

SELECT
    model_type,
    model_id,
    collection_name,
    file_name,
    JSON_UNQUOTE(JSON_EXTRACT(custom_properties, '$.bundled_asset_path')) AS bundled_asset_path,
    order_column
FROM media
WHERE (
        model_type = 'App\\Models\\Content\\Page\\InfoPage'
        AND model_id = @ac_career_page_id
        AND collection_name IN ('career_hero_image', 'career_gallery_images')
    )
    OR (
        model_type = 'App\\Models\\Content\\Service\\ServicePage'
        AND (
            (model_id = @ac_audit_page_id AND collection_name = 'service_hero_image')
            OR
            (model_id = @ac_services_index_page_id AND collection_name = 'services_index_audit_image')
        )
    )
ORDER BY model_type, model_id, collection_name, order_column;
