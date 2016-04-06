    <section id="the-top-homepage">
      <div class="social-sharing">
        <ul id="social-buttons">
          <li class="social-block"><a href="#" class="twitter"></a></li>
          <li class="social-block"><a href="#" class="facebook"></a></li>
          <li class="social-block"><a href="#" class="tumblr"></a></li>
        </ul>
      </div>
      <div class="middle-display">
        <span class="the-slogan">
          Buy, sell, or trade (almost) anything.<br>
          All over your campus.
        </span>
        <form id="click-to-start" action="{{ URL::route('account-create') }}" method="get">
          <span class="inlines"><input type="text" placeholder="Enter your .edu email address" id="edu-email" name="edu-email"></span>
          <span class="inlines"><input type="submit" class="start-button" value="Click to begin"></span>
          {{ Form::token() }}
        </form>
      </div>
    </section>
    <section id="the-about-section">
      <span class="about-title extra-padding">
        <h1 class="about-us-title">About BlackMarket <span class="green-u">U</span></h1>
      </span>
      <article class="the-description">
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. In dignissim nibh sit amet mollis gravida. Aliquam erat volutpat. In et facilisis ex. Quisque congue magna ex, id sodales nibh lacinia quis. Mauris ut suscipit felis. Duis at mauris non justo tempor feugiat. Nullam pretium dictum lectus nec bibendum.</p>
        <p>Nunc eu rutrum nisl. Pellentesque sed justo a libero mattis accumsan. Nullam fringilla molestie ex, eget cursus massa egestas vitae. Pellentesque nec nulla risus. Aliquam finibus ante quis erat scelerisque, in tristique nisi vulputate. Proin varius elit a maximus hendrerit. Duis in nunc justo. Nunc mollis nibh condimentum tortor lobortis, nec suscipit eros vehicula. Aenean nec suscipit sem. Fusce tincidunt est augue, eget rutrum nibh volutpat sed. Pellentesque interdum diam id ipsum rhoncus scelerisque. In varius vestibulum augue, non fringilla arcu facilisis nec. Proin ut nisi non augue luctus vestibulum eget at mauris. Cras ut hendrerit libero. Sed felis nisi, fermentum in turpis sed, bibendum interdum enim.</p>
        <p>Morbi fermentum, eros ut malesuada blandit, sem nibh interdum tortor, non commodo nisl eros non sem. Aliquam faucibus, nisl ac bibendum egestas, nibh turpis hendrerit dui, nec sodales arcu sem eget massa. Quisque mollis accumsan est nec varius. Nam quis lorem metus. Nunc sagittis massa ultricies, condimentum neque a, viverra velit. Nullam finibus nulla felis, nec maximus mauris malesuada in. In feugiat ut tortor ut cursus. Aenean viverra fermentum neque, id aliquet arcu placerat sodales. Vestibulum sit amet nisi erat. Suspendisse vel nibh euismod, pulvinar arcu eu, commodo dolor. Nullam quis neque quis nisl porta rhoncus id eu leo. Proin odio nunc, dictum et pretium in, suscipit sed dolor. Ut neque ligula, luctus eget placerat at, sodales tempus orci.</p>
      </article>
    </section>