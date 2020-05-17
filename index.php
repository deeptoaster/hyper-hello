<?
include(__DIR__ . '/../lib/include.php');
?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
<?
print_head('Hyperskelion');
?>  </head>
  <body>
    <div id="main">
      <h1>Hyperskelion</h1>
      <h2>Participant Registration</h2>
      <p>Welcome to the research study! To begin, please fill out the brief survey below. All fields are required.</p>
      <form>
        <div class="form-control">
          <label for="first">First name</label>
          <div class="input-group">
            <input type="text" id="first" />
          </div>
        </div>
        <div class="form-control">
          <label for="last">Last name</label>
          <div class="input-group">
            <input type="text" id="last" />
          </div>
        </div>
        <div class="form-control">
          <label for="birthday">Date of birth and class</label>
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
          <label for="memory">Favorite childhood memory</label>
          <div class="input-group">
            <textarea id="memory" rows="4"></textarea>
          </div>
        </div>
        <div class="form-control">
          <label for="fear">Greatest fear</label>
          <div class="input-group">
            <textarea id="fear" rows="4"></textarea>
          </div>
        </div>
        <div class="form-control">
          <div class="input-group input-group-left">
            <input type="checkbox" id="donor" />
            <label for="donor">I am an organ donor; my blood type is</label>
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
            <label for="snitch">I certify that I am not a member of the press, UN mission, or other watchdog organization</label>
          </div>
        </div>
        <div class="form-control">
          <div class="input-group">
            <input type="submit" value="Submit" />
          </div>
        </div>
      </form>
    </div>
  </body>
</html>
