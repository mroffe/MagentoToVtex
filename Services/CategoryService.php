<?php

class CategoryService {

    public static $categoryCounter = 0;

    public static function run(){

        self::categoryConsistencyCheck();

        $categoryTree = self::getCategoryTreeFromMagento();

        self::createCategoryTreeOnVtex($categoryTree);
    }

    private static function getCategoryTreeFromMagento(){
        return MagentoConnector::$ws->catalogCategoryTree(MagentoConnector::getActiveSession());
    }

    private static function categoryConsistencyCheck(){

        try{
            $result = VtexConnector::$ws->CategoryGet(1);

            if(!$result->CategoryGetResult)
                self::$categoryCounter += 1;

        }catch (Exception $e){
            Logger::log('Erro ao recuperar informacao da categoria.',$e,VtexConnector::$ws->__getLastRequest());
        }
    }

    private static function createCategoryTreeOnVtex($categoryTree){
        foreach($categoryTree->children[0]->children as $category)
            self::createCategories($category);
    }

    private static function createCategories($category, $parentId = null){

        if(!$category->is_active)
            return;

        try{
            $categoryEssence = MagentoConnector::$ws->catalogCategoryInfo(
                MagentoConnector::getActiveSession(),
                $category->category_id,
                null,
                null
            );
        }catch(SoapFault $e){
            Logger::log('Erro ao recuperar informacao da categoria.',$e,MagentoConnector::$ws->__getLastRequest());
        }

        $categoryData = new Category($categoryEssence, $parentId);

        //If category exists, update instead of creating a new one.
//        if($currentCategory = self::existsByName($categoryData))
//            $categoryData->Id = $currentCategory->CategoryGetByNameResult->Id;

        if(self::$categoryCounter){
            $categoryData->Id = 1;
            self::$categoryCounter = 0;
        }

        try{
            $result = VtexConnector::$ws->CategoryInsertUpdate($categoryData->toVtex());
            Logger::log("Categoria criada/atualizada: " . $result->CategoryInsertUpdateResult->Id . " - " . $result->CategoryInsertUpdateResult->Name);
        }catch (SoapFault $e){
            Logger::log('Erro ao inserir/atualizar categoria.',$e,VtexConnector::$ws->__getLastRequest());
        }

        if(count($category->children) > 0)
            foreach($category->children as $subCategory)
                self::createCategories($subCategory,$result->CategoryInsertUpdateResult->Id);
    }

    private static function existsByName($categoryData){
        try{
            $result = VtexConnector::$ws->CategoryGetByName(array("nameCategory" => $categoryData->Name));

            return ($result->CategoryGetByNameResult) ? $result : null;

        }catch (SoapFault $e){
            Logger::log('Erro ao verificar se categoria existe.',$e,VtexConnector::$ws->__getLastRequest());
        }
    }
}