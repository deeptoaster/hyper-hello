<?
define(
  'HYPER_STATUS_0800',
  "The time is now 8:00 Blacker Time.\nSelect a group to join."
);

include(__DIR__ . '/../lib/include.php');

function hyper_token($a, $b) {
  $tokens = array(
    array(
      'dramatic dolphin',
      'headbanging handmaiden',
      'emasculated emperor',
      'scandalous saxophonist',
      'shameful swan',
      'flatfooted farthing',
      'amalgamated ampersand',
      'leathery loafer',
      'meatiest marmot',
      'readjusted robber'
    ),
    array(
      'librarian lilith',
      'imbibing iguana',
      'subscription service',
      'libidinal leprechaun',
      'rubbernecking renter',
      'unbuffered unicorn',
      'ambiguous arbiter',
      'unbeholden usurper',
      'lubricated landmine',
      'zabajone zeal'
    ),
    array(
      'facial farter',
      'duckbilled dowry',
      'backcountry blowfish',
      'decadent diddler',
      'hickey hacker',
      'vociferous volunteer',
      'incognito ibis',
      'alcoholic anthrax',
      'sacrificial stingray',
      'lockjawed lisp'
    ),
    array(
      'radiant royalist',
      'bodybuilding borderline',
      'indoctrinating insurrection',
      'jaded jumpsuit',
      'midwestern militant',
      'redefining reluctance',
      'indignant imposter',
      'redshifted rangoon',
      'bedridden betrothed',
      'undejected umbrella'
    ),
    array(
      'overachieving onyx',
      'overburdened operator',
      'frescoed frenchman',
      'greedy grifter',
      'plebeian proletariat',
      'unenforceable utilitarianism',
      'freighter freedom',
      'diethyl disulfide',
      'poetic pillager',
      'kneejerk killer'
    ),
    array(
      'buffalo buffalo',
      'affable archbishop',
      'defecating doorstop',
      'infidel intruder',
      'offbeat ostracism',
      'lifeform likely',
      'infographic instigator',
      'offshoot opposition',
      'befriending butterflies',
      'lifejacket lore'
    ),
    array(
      'aggravating ardor',
      'vagabond vortex',
      'regicidal reputation',
      'rigid rotor',
      'argumentative apricot',
      'highfalutin horseradish',
      'zygogenetic zeal',
      'mugshot mime',
      'significant sauerkraut',
      'gigajoule gargoyle'
    ),
    array(
      'rehearsed retribution',
      'exhibitionist ellipsis',
      'ethical euthanasia',
      'dehydrated dimwit',
      'athletic allophone',
      'ash asunder',
      'unhygenic underwriter',
      'ophthalmologist organization',
      'ethnically eloquent',
      'jah jah'
    ),
  );

  return $tokens[$b][$a];
}

$config = array(
  'hyper_team_count' => 8,
  'hyper_team_overflow_count' => 2,
  'hyper_team_size' => 8,
  'hyper_db' => 'hyper.db',
  'hyper_fetch_statement' => 'hyper.sql',
  'hyper_overflow_flag' => 'hyper.flag',
  'hyper_status' => 'hyper.txt'
);

$alums = array(
  'Anne',
  'Diandra',
  'Tom',
  'Aaron S.',
  'Will L.',
  'Amy',
  'Kshu',
  'Julie'
);

$overflow_alums = array(
  'Aaron F.',
  'Xander'
);

$links = array(
  'mFgq264',
  'PnnjZtr',
  'zYfC8dG',
  '5WUZrgB',
  'du7jvBR',
  'FPyZaBg',
  'q56sKQu',
  'wkD23A2',
  'bXhnpyf',
  'pd8SQ6X'
);

session_start();
$create = !file_exists($config['hyper_db']);
$pdo = new PDO('sqlite:' . $config['hyper_db']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$team_max =
    $config['hyper_team_count'] + $config['hyper_team_overflow_count'] - 1;

if ($create) {
  $pdo->exec(
    <<<EOF
CREATE TABLE `users` (
  `id` integer PRIMARY KEY ASC,
  `name` varchar(41) UNIQUE NOT NULL,
  `class` integer NOT NULL,
  `memory` varchar(255) NOT NULL,
  `fear` varchar(255) NOT NULL,
  `blood` varchar(2) NOT NULL,
  `token` char(8) UNIQUE NOT NULL
)
EOF
  );

  $pdo->exec(
    <<<EOF
CREATE TABLE `assignments` (
  `team` integer NOT NULL,
  `user` integer UNIQUE NOT NULL
)
EOF
  );

  unset($_SESSION['hyper_class']);
  unset($_SESSION['hyper_id']);
  unset($_COOKIE['hyper_token']);
  setcookie('hyper_token', '', time());
}

if (!is_file($config['hyper_status'])) {
  file_put_contents($config['hyper_status'], HYPER_STATUS_0800);
}

if (!isset($_SESSION['hyper_id']) && isset($_COOKIE['hyper_token'])) {
  $statement = $pdo->prepare(
    <<<EOF
SELECT `id`,
  `class`
FROM `users`
WHERE `token` = :token
EOF
  );

  $statement->execute(array(
    ':token' => $_COOKIE['hyper_token']
  ));

  $row = $statement->fetch(PDO::FETCH_ASSOC);

  if ($row !== false) {
    $_SESSION['hyper_class'] = $row['class'];
    $_SESSION['hyper_id'] = $row['id'];
  }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $response = array();
  header('Content-Type: application/json');

  try {
    switch ($_POST['action']) {
      case 'join':
        if (!isset($_SESSION['hyper_id'])) {
          throw new BadFunctionCallException('You are not registered.');
        }

        if (!isset($_POST['team'])) {
          throw new BadFunctionCallException('Missing team selection.');
        }

        $team = (int)$_POST['team'];

        if ($team < 0 || $team > $team_max) {
          throw new OutOfBoundsException(
            "Team ID must be between 0 and $team_max."
          );
        }

        $pdo->exec('BEGIN EXCLUSIVE TRANSACTION');

        $statement = $pdo->prepare(
          <<<EOF
SELECT `users`.`class`,
COUNT(*)
FROM `assignments`
INNER JOIN `users` ON `users`.`id` = `assignments`.`user`
WHERE `assignments`.`team` = :team
GROUP BY `users`.`class`
EOF
        );

        $statement->execute(array(
          ':team' => $team
        ));

        $classes = $statement->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_KEY_PAIR);

        if (
          $_SESSION['hyper_class'] == 0 &&
              @$classes[0] >= $config['hyper_team_size'] -
              !(@$classes[1] + @$classes[2]) - 1
        ) {
          throw new OverflowException(
            'This team has too many frosh.'
          );
        }

        if (
          @$classes[1] + @$classes[2] + @$classes[1] >=
              $config['hyper_team_size']
        ) {
          throw new OverflowException(
            'This team is full.'
          );
        }

        $statement = $pdo->prepare(
          <<<EOF
INSERT OR REPLACE INTO `assignments` (
  `team`,
  `user`
)
VALUES (
  :team,
  :user
)
EOF
        );

        $statement->execute(array(
          ':team' => $team,
          ':user' => $_SESSION['hyper_id']
        ));

        $pdo->exec('COMMIT');
      case 'update':
        $statement =
            $pdo->prepare(file_get_contents($config['hyper_fetch_statement']));
        $statement->execute();
        $response['assignments'] =
            $statement->fetchAll(PDO::FETCH_COLUMN | PDO::FETCH_GROUP);

        $statement = $pdo->prepare(
          <<<EOF
SELECT `user`,
  `team`
FROM `assignments`
WHERE `team` = (
  SELECT `team`
  FROM `assignments`
  WHERE `user` = :user
)
ORDER BY `user`
EOF
        );

        $statement->execute(array(
          ':user' => @$_SESSION['hyper_id']
        ));

        $users = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (count($users)) {
          foreach ($users as $i => $user) {
            if ($user['user'] == $_SESSION['hyper_id']) {
              $response['selected'] = $user['team'];
              $response['link'] =
                  'https://discord.com/invite/' . $links[$user['team']];
              $response['token'] = hyper_token($user['team'], $i);
              break;
            }
          }
        }

        $response['message'] = trim(file_get_contents($config['hyper_status']));
        $response['overflow'] = file_exists($config['hyper_overflow_flag']);
        break;
      case 'register':
        if (!preg_match('/^[A-Za-z\'-]+( [A-Za-z\'-]+)+$/', $_POST['name'])) {
          throw new InvalidArgumentException(
            'First and last name must contain only letters, hyphens, and apostrophes.'
          );
        }

        if (!strlen($_POST['name']) >= 42) {
          throw new InvalidArgumentException(
            'Please enter a shorter name.'
          );
        }

        $class = (int)$_POST['class'];

        if ($class < 0 || $class >= 3) {
          throw new InvalidArgumentException('Invalid class selection.');
        }

        $memory = substr($_POST['memory'], 0, 255);
        $fear = substr($_POST['fear'], 0, 255);

        if (!in_array($_POST['blood'], array('A', 'B', 'AB', 'O'))) {
          throw new InvalidArgumentException('Invalid blood type selection.');
        }

        $token = md5(mt_rand());

        $statement = $pdo->prepare(
          <<<EOF
INSERT INTO `users` (
  `name`,
  `class`,
  `memory`,
  `fear`,
  `blood`,
  `token`
)
VALUES (
  :name,
  :class,
  :memory,
  :fear,
  :blood,
  :token
)
EOF
        );

        if (!is_writable($config['hyper_db'])) {
          throw new InvalidArgumentException(
            'Unable to write to database file.'
          );
        }

        $statement->execute(array(
          ':name' => $_POST['name'],
          ':class' => $class,
          ':memory' => $memory,
          ':fear' => $fear,
          ':blood' => $_POST['blood'],
          ':token' => $token
        ));

        $_SESSION['hyper_class'] = $class;
        $_SESSION['hyper_id'] = $pdo->lastInsertId();
        setcookie('hyper_token', $token, time() + 60 * 60 * 24 * 30);

        if (
          $_SESSION['hyper_id'] - 1 ==
              ($config['hyper_team_count'] - 0.5) * $config['hyper_team_size']
        ) {
          touch($_SESSION['hyper_overflow_flag']);
        }

        $response['message'] = 'success';
        break;
    }
  } catch (Exception $e) {
    header('HTTP/1.1 400 Bad Request');
    header('Status: 400 Bad Request');
    $response['message'] = $e->getMessage();
  }

  die(json_encode($response));
}
?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
<?
print_head('Hyperskelion');
?>    <link href="hyper.css" rel="stylesheet" />
    <link href="//fonts.googleapis.com/css?family=Share+Tech+Mono|Audiowide" rel="stylesheet" type="text/css">
    <script type="text/javascript">// <![CDATA[
<?
if (!isset($_SESSION['hyper_id'])) {
  echo <<<EOF
      var \$error = $('<div class="error" />');

      function prefail(message) {
        $('#main h1').after(\$error.text(message));
        $('#main').scrollTop(0);
      }


EOF;
}

echo <<<EOF
      var statusCache = null;
      var timeout = 0;
      var \$group;
      var \$link;
      var \$status;
      var \$token;
      var trigger = 'The time is now 8:05 Blacker Time.';

      function postfail(message) {
        clearTimeout(timeout);

        if (statusCache == null) {
          statusCache = \$status.text();
        }

        \$status.addClass('error').text(message);

        timeout = setTimeout(function() {
          \$status.removeClass('error').text(statusCache);
          statusCache = null;
        }, 2500);
      }

      function poll() {
        $.post('./', {action: 'update'}).done(update).fail(function(xhr) {
          postfail(xhr.responseJSON.message);
        });
      }

      function update(data) {
        var \$cells = $('.console-cell');

        for (var i = 0; i <= $team_max; i++) {
          \$cells
              .eq(i)
              .toggleClass('selected', data.selected == i)
              .find('li')
              .slice(1)
              .text(function(j) {
            return (data.assignments[i] || [])[j] || '';
          });
        }

        \$group.text(data.selected);
        \$link.text(data.link).attr('href', data.link);
        \$token.val(data.token);

        if (statusCache == null) {
          \$status.text(data.message);
        } else {
          statusCache = data.message;
        }

        $(document.body)
            .toggleClass('overflow', data.overflow)
            .toggleClass(
          'complete',
          data.message.slice(0, trigger.length) == trigger
        );
      }

      $(function() {
        \$group = $('.console-status b');
        \$link = $('.console-status a');
        \$status = $('.console-status p:first-child');
        \$token = $('.console-status input');

        \$token.click(function() {
          this.select();
        });

        $('.console-cell-outer').click(function() {
          var \$active = $(this).parent().addClass('active');

          $.post('./', {
            action: 'join',
            team: \$active.index()
          }).done(function(data) {
            update(data);
            \$active.removeClass('active');
          }).fail(function(xhr) {
            postfail(xhr.responseJSON.message);
            \$active.removeClass('active');
          });
        });


EOF;

if (!isset($_SESSION['hyper_id'])) {
  echo <<<EOF
        $('.glitchable').attr('data-text', function() {
          return $(this).text();
        });

        $(document).mousemove(function(e) {
          document.body.style.backgroundPosition = -e.clientX / 40 + 'px';
        });

        $('#main input').change(function() {
          if ($('#main input').filter(function() {
            return $(this).val();
          }).length >= 5) {
            setTimeout(function() {
              $('#main').addClass('glitching');
            }, 1000);
          }
        });

        $('form').submit(function() {
          \$error.detach();

          if ($('#main input[type=text], textarea').filter(function() {
            return !$(this).val();
          }).length !== 0) {
            prefail('Please ensure that all fields contain a valid response.');
          } else if ($('#main input[type=checkbox]').filter(function() {
            return !$(this).prop('checked');
          }).length !== 0) {
            prefail('Please ensure that all checkboxes are checked.');
          } else {
            $.post('./', {
              action: 'register',
              name: $('#first').val().trim() + ' ' + $('#last').val().trim(),
              class: $('#class').val(),
              memory: $('#memory').val().trim(),
              fear: $('#fear').val().trim(),
              blood: $('#blood').val()
            }).done(function(data) {
              if (data.message != 'success') {
                prefail('An unknown error occurred.');
              } else {
                $(document.body).addClass('console');
                setInterval(poll, 10000);
                poll();
              }
            }).fail(function(xhr) {
              prefail(xhr.responseJSON.message);
            });
          }

          return false;
        });

EOF;
} else {
  echo <<<EOF
        setInterval(poll, 5000);
        poll();

EOF;
}
?>      });
    // ]]></script>
  </head>
<?
if (isset($_SESSION['hyper_id'])) {
  echo <<<EOF
  <body class="console">

EOF;
} else {
  echo <<<EOF
  <body>
    <div id="main">
      <h1 class="glitchable">Hyperskelion</h1>
      <h2 class="glitchable">Participant Registration</h2>
      <p class="glitchable">Welcome to the research study! To begin, please fill out the brief survey below. All fields are required.</p>
      <form>
        <div class="form-control">
          <label for="first" class="glitchable">First name</label>
          <div class="input-group">
            <input type="text" id="first" maxlength="20" />
          </div>
        </div>
        <div class="form-control">
          <label for="last" class="glitchable">Last name</label>
          <div class="input-group">
            <input type="text" id="last" maxlength="20" />
          </div>
        </div>
        <div class="form-control">
          <label for="birthday" class="glitchable">Date of birth and class</label>
          <div class="input-group input-group-left">
            <input type="text" id="birthday" />
          </div>
          <div class="input-group input-group-right">
            <select id="class">
              <option value="0">Frosh</option>
              <option value="1">Smore</option>
              <option value="2">Junior</option>
            </select>
          </div>
        </div>
        <div class="form-control">
          <label for="memory" class="glitchable">Favorite childhood memory</label>
          <div class="input-group">
            <textarea id="memory" rows="4" maxlength="255"></textarea>
          </div>
        </div>
        <div class="form-control">
          <label for="fear" class="glitchable">Greatest fear</label>
          <div class="input-group">
            <textarea id="fear" rows="4" maxlength="255"></textarea>
          </div>
        </div>
        <div class="form-control">
          <div class="input-group input-group-left">
            <input type="checkbox" id="donor" />
            <label for="donor" class="glitchable">I am an organ donor; my blood type is</label>
          </div>
          <div class="input-group input-group-right">
            <select id="blood">
              <option value="A">A</option>
              <option value="B">B</option>
              <option value="AB">AB</option>
              <option value="O">O</option>
            </select>
          </div>
        </div>
        <div class="form-control">
          <div class="input-group">
            <input type="checkbox" id="snitch" name="donor" />
            <label for="snitch" class="glitchable">I certify that I am not a member of the press, UN mission, or other watchdog organization</label>
          </div>
        </div>
        <div class="form-control">
          <div class="input-group">
            <input class="btn-persistent" type="submit" value="Submit" />
          </div>
        </div>
      </form>

EOF;

  print_footer(
    'Sarah Crucilla, Alex Cui, Erik Herrera, Brendan Hollaway, Anant Kale',
    'David Kornfeld, Nikita Kosolobov, Tye Lamkin, Mei-Ling Laures',
    'Sierra Lopezalles, Qiaoqiao Mu, Ray Sun, Bethany Suter'
  );

  echo <<<EOF
    </div>

EOF;
}
?>    <div id="console">
      <div class="console-block console-delay-0"></div>
      <div class="console-block console-delay-1"></div>
      <div class="console-block console-delay-2"></div>
      <div class="console-block console-delay-3"></div>
      <div class="console-centerpiece">
        <div class="console-ring">
          <div>
            <img class="trim" src="trim.png" alt="" />
            <img class="text" src="text.png" alt="" />
            <img class="logo" src="logo.png" alt="" />
          </div>
        </div>
      </div>
      <div class="console-content">
        <div class="console-grid">
<?
$order = range(0, $config['hyper_team_count'] - 1);
shuffle($order);
$segment = $config['hyper_team_count'] / $config['hyper_team_overflow_count'];

$lis = str_repeat(
  <<<EOF
                  <li></li>

EOF
  ,
  $config['hyper_team_size']
);

foreach ($order as $i => $item) {
  $delay = floor($item / 3);
  $alum = $alums[$i];

  echo <<<EOF
          <div class="console-cell console-delay-$delay">
            <div class="console-cell-outer">
              <div class="console-cell-inner">
                <ul>
                  <li>$alum (alum)</li>
$lis                </ul>
                </div>
            </div>
          </div>

EOF;

  if (!(($i + 1) % $segment)) {
    $alum = $overflow_alums[($i + 1) / $segment - 1];

    echo <<<EOF
          <div class="console-cell console-cell-overflow">
            <div class="console-cell-outer">
              <div class="console-cell-inner">
                <ul>
                  <li>$alum (overflow)</li>
$lis                </ul>
                </div>
            </div>
          </div>

EOF;

  }
}
?>        </div>
      </div>
      <div class="console-shield"></div>
      <div class="console-status">
        <p></p>
        <p>To continue, join <a href=""></a>.</p>
        <p>Your group number is <b></b>. Here is your personal token:</p>
        <input type="text" readonly="readonly" />
        <p>If you forget, you can always come back to this page.</p>
      </div>
    </div>
  </body>
</html>
