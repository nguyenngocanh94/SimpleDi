<?php


namespace SimpleDi\Generator;


use Exception;
use SimpleDi\Annotations\AnnotationReader;

/**
 * assume that outside of vendor is your code folder.
 * let begin define your src structure from that point
 * Class AutoGenerator
 * @package SimpleDi\Generator
 */
class AutoGenerator
{
    const outSideOfVendor = '.../../../../';
    private string $src;
    private Parser $parser;
    const phpTag = "<?php \n\nuse SimpleDi\Registry\SimpleDi; \n \n";

    function __construct(string $src, string $proxies)
    {
        $this->src = self::outSideOfVendor.$src;
        $this->proxies = self::outSideOfVendor.$proxies;
        $this->parser = new Parser();
    }

    public function write(){
        if (file_exists($this->proxies)){
            unlink($this->proxies);
        }
        try {
            fopen($this->proxies,'w');
            file_put_contents($this->proxies, self::phpTag.'return ['."\n");
            try {
                self::findAllFileInFolder($this->src);
            }catch (Exception $e){
                $cursor = fopen($this->proxies, 'a');
                fwrite($cursor, "];");
            }
            $cursor = fopen($this->proxies, 'a');
            fwrite($cursor, "];");
        }catch (Exception $e){
            unlink($this->proxies);
        }
    }

    private function findAllFileInFolder(string $dir){
        $ffs = scandir($dir);

        unset($ffs[array_search('.', $ffs, true)]);
        unset($ffs[array_search('..', $ffs, true)]);

        // prevent empty ordered elements
        if (count($ffs) < 1)
            return;

        foreach($ffs as $ff){
            if(is_dir($dir.'/'.$ff)){
                self::findAllFileInFolder($dir.'/'.$ff);
            }else{
                self::readAndWriteBean($dir.'/'.$ff);
            }
        }

    }

    private function readAndWriteBean(string $ff){
        $arr = $this->parser->extractPhpClasses($ff);
        if (count($arr)>0){
            $className = $arr[0];
            $content = "";
            $file = fopen($this->proxies, 'a');
            try {
                $annotation = AnnotationReader::getMarkExport($className);
                if ($annotation!=null){
                    if ($annotation->typeOf!=""){
                        $content .= "\t".$annotation->typeOf.'::class'.'=>['.$className.'::class, SimpleDi::'.$annotation->scope."],\n";
                    }else{
                        $content .= "\t".$className.'::class'.'=>['.$className.'::class, SimpleDi::'.$annotation->scope."],\n";
                    }

                    fwrite($file, $content);
                }
            }catch (\Throwable $e){
                echo $e->getMessage()." at class:".$className." file: ".$ff."\n";
            }
        }
    }
}