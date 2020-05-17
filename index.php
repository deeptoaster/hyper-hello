<?
include(__DIR__ . '/../lib/include.php');
?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
<?
print_head('Hyperskelion');
?>    <link href="hyper.css" rel="stylesheet" />
    <script type="text/javascript">// <![CDATA[
      $(function() {
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
          $(document.body).addClass('console');
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
              <option value="frosh">Frosh</option>
              <option value="smore">Smore</option>
              <option value="junior">Junior</option>
            </select>
          </div>
        </div>
        <div class="form-control">
          <label for="memory" class="glitchable">Favorite childhood memory</label>
          <div class="input-group">
            <textarea id="memory" rows="4"></textarea>
          </div>
        </div>
        <div class="form-control">
          <label for="fear" class="glitchable">Greatest fear</label>
          <div class="input-group">
            <textarea id="fear" rows="4"></textarea>
          </div>
        </div>
        <div class="form-control">
          <div class="input-group input-group-left">
            <input type="checkbox" id="donor" />
            <label for="donor" class="glitchable">I am an organ donor; my blood type is</label>
          </div>
          <div class="input-group input-group-right">
            <select>
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
            <input type="submit" value="Submit" />
          </div>
        </div>
      </form>
    </div>
    <div id="console">
      <div class="console-centerpiece">
        <div class="console-ring">
      </div>
      <div class="console-grid"></div>
    </div>
  </body>
</html>
