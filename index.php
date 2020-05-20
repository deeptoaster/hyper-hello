<?
include(__DIR__ . '/../lib/include.php');

$config = array(
  'hyper_team_count' => 10,
  'hyper_db' => 'hyper.db'
);

session_start();
$create = !file_exists($config['hyper_db']);
$pdo = new PDO('sqlite:' . $config['hyper_db']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($create) {
  $pdo->exec(
    <<<EOF
CREATE TABLE `users` (
  `id` integer PRIMARY KEY ASC,
  `name` varchar(64) UNIQUE NOT NULL,
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
}

if (!isset($_SESSION['hyper_id']) && isset($_COOKIE['hyper_token'])) {
  $statement = $pdo->prepare(
    <<<EOF
SELECT `id`
FROM `users`
WHERE `token` = :token
EOF
  );

  $statement->execute(array(
    ':token' => $_COOKIE['hyper_token']
  ));

  $id = $statement->fetch(PDO::FETCH_COLUMN);

  if ($id !== false) {
    $_SESSION['hyper_id'] = $id;
  }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $response = array();
  header('Content-Type: application/json');

  try {
    switch ($_POST['action']) {
      case 'join':
      case 'update':
        $statement = $pdo->prepare(file_get_contents('fetch.sql'));
        $statement->execute();

        $response['assignments'] = json_encode(
          $statement->fetchAll(PDO::FETCH_COLUMN | PDO::FETCH_GROUP)
        );

        break;
      case 'register':
        if (!preg_match('/^[A-Za-z\'-]+( [A-Za-z\'-]+)+$/', $_POST['name'])) {
          throw new InvalidArgumentException(
            'First and last name must contain only letters, hyphens, and apostrophes.'
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

        $_SESSION['hyper_id'] = $pdo->lastInsertId();
        setcookie('hyper_token', $token, time() + 60 * 60 * 24 * 30);
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

      function fail(message) {
        $('#main h1').after(\$error.text(message));
        $('#main').scrollTop(0);
      }


EOF;
}

echo <<<EOF
      function poll() {
        $.post('./', {action: 'update'}, update);
      }

      function update(data) {
        var \$cells = $('.console-cell');

        for (var i = 0; i < $config[hyper_team_count]; i++) {
          \$cells.eq(i).find('li').text(function(j) {
            (data.assignments[i] || [])[j];
          });
        }
      }

      $(function() {
        $('.console-cell').click(function() {
          $(this).addClass('active');
          $.post('./', {action: 'join', team: $(this).index()}, update);
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

        $('input').change(function() {
          if ($('input').filter(function() {
            return $(this).val();
          }).length >= 5) {
            setTimeout(function() {
              $('#main').addClass('glitching');
            }, 1000);
          }
        });

        $('form').submit(function() {
          \$error.detach();

          if ($('input[type=text], textarea').filter(function() {
            return !$(this).val();
          }).length !== 0) {
            fail('Please ensure that all fields contain a valid response.');
          } else if ($('input[type=checkbox]').filter(function() {
            return !$(this).prop('checked');
          }).length !== 0) {
            fail('Please ensure that all checkboxes are checked.');
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
                fail('An unknown error occurred.');
              } else {
                $(document.body).addClass('console');
                setInterval(poll, 10000);
              }
            }).fail(function(xhr) {
              fail(xhr.responseJSON.message);
            });
          }

          return false;
        });

EOF;
} else {
  echo <<<EOF
        setInterval(poll, 10000);

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
            <input type="text" id="first" />
          </div>
        </div>
        <div class="form-control">
          <label for="last" class="glitchable">Last name</label>
          <div class="input-group">
            <input type="text" id="last" />
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
      <div class="console-grid">
        <div class="console-cell console-delay-2">
          <div class="console-cell-outer">
            <div class="console-cell-inner">
              <ul>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
              </ul>
            </div>
          </div>
        </div>
        <div class="console-cell console-delay-0">
          <div class="console-cell-outer">
            <div class="console-cell-inner">
              <ul>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
              </ul>
            </div>
          </div>
        </div>
        <div class="console-cell console-delay-1">
          <div class="console-cell-outer">
            <div class="console-cell-inner">
              <ul>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
              </ul>
            </div>
          </div>
        </div>
        <div class="console-cell console-delay-2">
          <div class="console-cell-outer">
            <div class="console-cell-inner">
              <ul>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
              </ul>
            </div>
          </div>
        </div>
        <div class="console-cell console-delay-0">
          <div class="console-cell-outer">
            <div class="console-cell-inner">
              <ul>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
              </ul>
            </div>
          </div>
        </div>
        <div class="console-cell console-delay-0">
          <div class="console-cell-outer">
            <div class="console-cell-inner">
              <ul>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
              </ul>
            </div>
          </div>
        </div>
        <div class="console-cell console-delay-1">
          <div class="console-cell-outer">
            <div class="console-cell-inner">
              <ul>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
              </ul>
            </div>
          </div>
        </div>
        <div class="console-cell console-delay-2">
          <div class="console-cell-outer">
            <div class="console-cell-inner">
              <ul>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
              </ul>
            </div>
          </div>
        </div>
        <div class="console-cell console-delay-2">
          <div class="console-cell-outer">
            <div class="console-cell-inner">
              <ul>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
              </ul>
            </div>
          </div>
        </div>
        <div class="console-cell console-delay-1">
          <div class="console-cell-outer">
            <div class="console-cell-inner">
              <ul>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
