<?php

namespace spec\Jlapp\SmartSeeder;

use Illuminate\Database\ConnectionResolverInterface;
use PhpSpec\ObjectBehavior;

class SmartSeederRepositorySpec extends ObjectBehavior
{
    /** @var string */
    protected $table = 'test-table-name';

    /** @var string */
    protected $source = 'mysql';

    /** @var string */
    protected $env = 'production';

    /** @var ConnectionResolverInterface */
    protected $resolver;

    public function let(ConnectionResolverInterface $resolver)
    {
        $this->resolver = $resolver;

        $this->beConstructedWith($this->resolver, $this->table);
        $this->setSource($this->source);
        $this->setEnv($this->env);

        $this->shouldHaveType('Jlapp\SmartSeeder\SmartSeederRepository');
    }

    public function it_should_implement_interface()
    {
        $this->beAnInstanceOf('Illuminate\Database\Migrations\MigrationRepositoryInterface');
    }

    public function it_should_provide_resolver()
    {
        $this->getConnectionResolver()->shouldBe($this->resolver);
    }

    public function it_should_get_connection_for_source()
    {
        $connection = microtime();
        $this->resolver->connection($this->source)->willReturn($connection);

        $this->getConnection()->shouldBe($connection);
    }
}
