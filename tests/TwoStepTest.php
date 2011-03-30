<?php
namespace aura\view;
use aura\di\Forge;
use aura\di\Config;

/**
 * Test class for TwoStep.
 * Generated by PHPUnit on 2011-03-27 at 14:45:04.
 */
class TwoStepTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TwoStep
     */
    protected $twostep;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        
        $template = new Template(
            new Finder,
            new PluginRegistry(new Forge(new Config))
        );
        
        $this->twostep = new TwoStep($template);
        
        // prepare a set of directories for paths
        $base = __DIR__ . DIRECTORY_SEPARATOR . 'tmp';
        $dirs = array('foo', 'bar', 'baz');
        foreach ($dirs as $dir) {
            $this->dirs[$dir] = $base . DIRECTORY_SEPARATOR . $dir;
            mkdir($this->dirs[$dir], 0777, true);
        }
        
        // put a view in 'foo'
        $file = $this->dirs['foo'] . DIRECTORY_SEPARATOR . 'view_name.php';
        $code = '<?php echo $this->view_var; ?>';
        file_put_contents($file, $code);
        
        // put a layout in 'baz'
        $file = $this->dirs['baz'] . DIRECTORY_SEPARATOR . 'layout_name.php';
        $code = '<div><?php echo $this->layout_var . $this->content_var; ?></div>';
        file_put_contents($file, $code);
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        parent::tearDown();
        
        // remove the files and directories
        unlink($this->dirs['foo'] . DIRECTORY_SEPARATOR . 'view_name.php');
        unlink($this->dirs['baz'] . DIRECTORY_SEPARATOR . 'layout_name.php');
        foreach ($this->dirs as $dir) {
            rmdir($dir);
        }
    }

    /**
     * @todo Implement testRender().
     */
    public function testRender()
    {
        $this->twostep->setViewName('view_name');
        $this->twostep->setViewData(array('view_var' => 'World!'));
        $this->twostep->setViewPaths(array($this->dirs['bar'], $this->dirs['foo']));
        $this->twostep->setLayoutName('layout_name');
        $this->twostep->setLayoutData(array('layout_var' => 'Hello '));
        $this->twostep->setLayoutPaths(array($this->dirs['bar'], $this->dirs['baz']));
        $this->twostep->setContentVar('content_var');
        
        $expect = '<div>Hello World!</div>';
        $actual = $this->twostep->render();
        $this->assertSame($expect, $actual);
    }
    
    public function testRednerNoLayout()
    {
        $this->twostep->setViewName('view_name');
        $this->twostep->setViewData(array('view_var' => 'World!'));
        $this->twostep->setViewPaths(array($this->dirs['bar'], $this->dirs['foo']));
        
        $expect = 'World!';
        $actual = $this->twostep->render();
        $this->assertSame($expect, $actual);
    }
    
    public function testRenderNoView()
    {
        $this->twostep->setLayoutName('layout_name');
        $this->twostep->setLayoutData(array('layout_var' => 'Hello '));
        $this->twostep->setLayoutPaths(array($this->dirs['bar'], $this->dirs['baz']));
        $this->twostep->setContentVar('content_var');
        
        $expect = '<div>Hello </div>';
        $actual = $this->twostep->render();
        $this->assertSame($expect, $actual);
    }
    
    public function testRenderNothing()
    {
        $actual = $this->twostep->render();
        $this->assertNull($actual);
    }
}
