<?php partial('head'); ?>
<?php disableCache(); ?>
<body>
    <header>
        <h1>Questo è il titolo principale della pagina</h1>
        <nav>
            <ul>
                <li><a href="#testo">Vai alla sezione testo</a></li>
                <li><a href="#media">Vai alla sezione media</a></li>
                <li><a href="#tabelle">Vai alla sezione tabelle</a></li>
                <li><a href="#form">Vai alla sezione form</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <article>
            <section id="testo">
                <h2>Una sezione dedicata al testo</h2>
                <p><strong>Lorem ipsum dolor sit amet</strong>, consectetur adipiscing elit. <em>Maecenas sed diam eget risus varius blandit sit amet non magna.</em> <u>Cras mattis consectetur purus sit amet fermentum.</u> <small>Questo è un testo piccolo.</small> Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit. <mark>Questo testo è evidenziato.</mark></p>
                <p>Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. <abbr title="per esempio">p.e.</abbr>, Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus.</p>
                
                <pre>
    Questo è un testo preformattato.
    Mantiene gli spazi      e le interruzioni di riga.
    <code>
        // Questo è un esempio di codice
        function saluta() {
            console.log("Ciao, mondo!");
        }
    </code>
                </pre>
                
                <blockquote>
                    <p>"La conoscenza parla, ma la saggezza ascolta."</p>
                    <cite>- Jimi Hendrix</cite>
                </blockquote>

                <div>
                    <h3>Una lista non ordinata:</h3>
                    <ul>
                        <li>Primo elemento</li>
                        <li>Secondo elemento</li>
                        <li>Terzo elemento</li>
                    </ul>

                    <h3>Una lista ordinata:</h3>
                    <ol>
                        <li>Primo passo</li>
                        <li>Secondo passo</li>
                        <li>Terzo passo</li>
                    </ol>
                    
                    <h3>Una lista di definizioni:</h3>
                    <dl>
                        <dt>HTML</dt>
                        <dd>HyperText Markup Language</dd>
                        <dt>CSS</dt>
                        <dd>Cascading Style Sheets</dd>
                    </dl>
                </div>
            </section>

            <hr>

            <section id="media">
                <h2>Una sezione dedicata ai media</h2>
                <p>Qui sotto un'immagine, un audio e un video di esempio.</p>
                
                <figure>
                    <img src="https://via.placeholder.com/400x200" alt="Immagine di esempio">
                    <figcaption>Didascalia dell'immagine.</figcaption>
                </figure>
                
                <audio controls>
                    <source src="horse.ogg" type="audio/ogg">
                    <source src="horse.mp3" type="audio/mpeg">
                    Il tuo browser non supporta il tag audio.
                </audio>
                
                <video width="320" height="240" controls>
                    <source src="movie.mp4" type="video/mp4">
                    <source src="movie.ogg" type="video/ogg">
                    Il tuo browser non supporta il tag video.
                </video>
            </section>

            <hr>

            <section id="tabelle">
                <h2>Una sezione con una tabella</h2>
                <table border="1">
                    <caption>Riepilogo mensile</caption>
                    <thead>
                        <tr>
                            <th>Mese</th>
                            <th>Entrate</th>
                            <th>Uscite</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Gennaio</td>
                            <td>€1,500</td>
                            <td>€800</td>
                        </tr>
                        <tr>
                            <td>Febbraio</td>
                            <td>€1,800</td>
                            <td>€950</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>Totale</td>
                            <td>€3,300</td>
                            <td>€1,750</td>
                        </tr>
                    </tfoot>
                </table>
            </section>
            
            <hr>

            <section id="form">
                <h2>Un esempio di form</h2>
                <form action="/submit-form" method="post">
                    <fieldset>
                        <legend>Informazioni personali</legend>
                        <label for="fname">Nome:</label><br>
                        <input type="text" id="fname" name="fname" value="Mario"><br>
                        <label for="lname">Cognome:</label><br>
                        <input type="text" id="lname" name="lname" value="Rossi"><br><br>
                        
                        <label for="email">Email:</label><br>
                        <input type="email" id="email" name="email"><br><br>
                        
                        <label for="password">Password:</label><br>
                        <input type="password" id="password" name="password"><br><br>
                        
                        <p>Scegli il tuo colore preferito:</p>
                        <input type="radio" id="rosso" name="colore" value="Rosso">
                        <label for="rosso">Rosso</label><br>
                        <input type="radio" id="verde" name="colore" value="Verde">
                        <label for="verde">Verde</label><br>
                        
                        <p>Seleziona i tuoi interessi:</p>
                        <input type="checkbox" id="sport" name="interessi" value="Sport">
                        <label for="sport">Sport</label><br>
                        <input type="checkbox" id="musica" name="interessi" value="Musica">
                        <label for="musica">Musica</label><br><br>
                        
                        <label for="cars">Scegli un'auto:</label>
                        <select id="cars" name="cars">
                            <option value="volvo">Volvo</option>
                            <option value="saab">Saab</option>
                            <option value="fiat" selected>Fiat</option>
                            <option value="audi">Audi</option>
                        </select><br><br>
                        
                        <label for="messaggio">Lascia un messaggio:</label><br>
                        <textarea id="messaggio" name="messaggio" rows="4" cols="50"></textarea><br><br>
                        
                        <input type="submit" value="Invia">
                        <button type="reset">Cancella</button>
                    </fieldset>
                </form>
            </section>
        </article>
        
        <aside>
            <h3>Questo è un contenuto a latere (aside)</h3>
            <p>Potrebbe contenere link correlati o pubblicità.</p>
        </aside>
    </main>

    <footer>
        <address>
            Scritto da <a href="mailto:webmaster@example.com">John Doe</a>.<br> 
            Visita il nostro sito:<br>
            Example.com<br>
            Box 564, Disneyland<br>
            USA
        </address>
        <p><time datetime="2025-07-30">30 Luglio 2025</time></p>
        <details>
            <summary>Copyright 2025</summary>
            <p> - Tutti i diritti riservati.</p>
        </details>
    </footer>

</body>
<?php partial('footer'); ?>