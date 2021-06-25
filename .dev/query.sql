select cod_fornecedor,
       cod_prod,
       cliente,
       documento,
       nome_prod,
       nome_categoria,
       nome_fornecedor,
       valor_original,
       data_compra,
       pa.discount as valor_desconto,
       pa.value    as valor_final,
       pa.date     as data_pgto,
       data_devolucao,
       status_situacao,
       pa.status   as status_pgto,
       pa.rate     as taxa_aplicada,
       taxa_original
from (
         select pur.id,
                sup.code as cod_fornecedor,
                pro.cod_prod,
                cus.name as cliente,
                cus.document as documento,
                pro.nome_prod,
                pro.nome_categoria,
                sup.name as nome_fornecedor,
                pur.value as valor_original,
                pur.date as data_compra,
                pur.returned_at as data_devolucao,
                pur.status as status_situacao,
                pur.rate as taxa_original
         from purchases pur
                  inner join suppliers sup on pur.supplier_id = sup.id
                  inner join (select pro.id as producto_id,
                                     pro.code as cod_prod,
                                     pro.name as nome_prod,
                                     cat.name as nome_categoria
                              from products pro
                                       inner join categories cat on pro.category_id = cat.id) pro
                             on pur.product_id = pro.producto_id
                  inner join customers cus on pur.customer_id = cus.id
         where pur.batch_uuid = '75d06ac9-95a7-4db0-8c6e-ba3e15a82c76'
     ) p
         inner join payments pa on pa.purchase_id = p.id;