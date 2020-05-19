<?
include(__DIR__ . '/../lib/include.php');

function hyper_fail($message) {
  header('HTTP/1.1 400 Bad Request');
  header('Status: 400 Bad Request');
  die($message);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (!preg_match('/^[A-Za-z\'-]+( [A-Za-z\'-]+)+$/', $_POST['name'])) {
    hyper_fail(
      'First and last name must contain only letters, hyphens, and apostrophes.'
    );
  }

  $class = (int)$_POST['class'];

  if ($class < 0 || $class >= 3) {
    hyper_fail('Invalid class selection.');
  }

  $memory = substr($_POST['memory'], 0, 255);
  $fear = substr($_POST['fear'], 0, 255);

  if (!in_array($_POST['blood'], array('A', 'B', 'AB', 'O'))) {
    hyper_fail('Invalid blood type selection.');
  }

  die();
}
?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
<?
print_head('Hyperskelion');
?>    <link href="hyper.css" rel="stylesheet" />
    <script type="text/javascript">// <![CDATA[
      $(function() {
        var checkboxError = $(
          '<div class="error">Please ensure that all checkboxes are checked.</div>'
        );

        var emptyError = $(
          '<div class="error">Please ensure that all fields contain a valid response.</div>'
        );

        var genericError = $('<div class="error" />');

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
          checkboxError.detach();
          emptyError.detach();
          genericError.detach();

          if ($('input[type=text], textarea').filter(function() {
            return !$(this).val();
          }).length !== 0) {
            $('#main h1').after(emptyError);
            $('#main').scrollTop(0);
          } else if ($('input[type=checkbox]').filter(function() {
            return !$(this).prop('checked');
          }).length !== 0) {
            $('#main h1').after(checkboxError);
            $('#main').scrollTop(0);
          } else {
            $.post('./', {
              name: $('#first').val().trim() + ' ' + $('#last').val().trim(),
              class: $('#class').val(),
              memory: $('#memory').val().trim(),
              fear: $('#fear').val().trim(),
              blood: $('#blood').val()
            }).done(function() {
              $(document.body).addClass('console');
            }).fail(function(e) {
              $('#main h1').after(genericError.text(e.responseText));
              $(document).scrollTop(0);
            });
          }

          return false;
        })
      });
    // ]]></script>
  </head>
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
              <option value="a">A</option>
              <option value="b">B</option>
              <option value="ab">AB</option>
              <option value="o">O</option>
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
    <div id="console">
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
            <div class="console-cell-inner"></div>
          </div>
        </div>
        <div class="console-cell console-delay-0">
          <div class="console-cell-outer">
            <div class="console-cell-inner"></div>
          </div>
        </div>
        <div class="console-cell console-delay-1">
          <div class="console-cell-outer">
            <div class="console-cell-inner"></div>
          </div>
        </div>
        <div class="console-cell console-delay-2">
          <div class="console-cell-outer">
            <div class="console-cell-inner"></div>
          </div>
        </div>
        <div class="console-cell console-delay-0">
          <div class="console-cell-outer">
            <div class="console-cell-inner"></div>
          </div>
        </div>
        <div class="console-cell console-delay-0">
          <div class="console-cell-outer">
            <div class="console-cell-inner"></div>
          </div>
        </div>
        <div class="console-cell console-delay-1">
          <div class="console-cell-outer">
            <div class="console-cell-inner"></div>
          </div>
        </div>
        <div class="console-cell console-delay-2">
          <div class="console-cell-outer">
            <div class="console-cell-inner"></div>
          </div>
        </div>
        <div class="console-cell console-delay-2">
          <div class="console-cell-outer">
            <div class="console-cell-inner"></div>
          </div>
        </div>
        <div class="console-cell console-delay-1">
          <div class="console-cell-outer">
            <div class="console-cell-inner"></div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
