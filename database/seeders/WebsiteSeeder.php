<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Event;
use App\Models\Post;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WebsiteSeeder extends Seeder
{
    public function run(): void
    {
        Book::create([
            'title' => 'Titolo del libro',
            'slug' => Str::slug('Titolo del libro'),
            'status' => 'available',
            'genre' => 'Romanzo contemporaneo',
            'description' => 'Una breve descrizione del libro, del genere e dell’atmosfera narrativa.',
            'synopsis' => 'Una storia intima sul modo in cui i ricordi cambiano forma quando incontrano il presente. Tra luoghi familiari, silenzi e piccole rivelazioni, la protagonista impara a riconoscere ciò che resta quando tutto sembra spostarsi.',
            'excerpt' => 'La casa aveva il rumore quieto delle cose lasciate al loro posto. Sul tavolo, una tazza fredda e un biglietto piegato dicevano più di quanto lei fosse pronta ad ascoltare.',
            'reviews' => [
                ['quote' => 'Una voce delicata, precisa, capace di rendere memorabili i dettagli.', 'author' => 'Lettura in anteprima'],
                ['quote' => 'Un romanzo raccolto e luminoso, da leggere lentamente.', 'author' => 'Nota editoriale'],
            ],
            'cover' => 'images/books/libro-1.jpg',
            'amazon_url' => 'https://www.amazon.it/',
            'is_featured' => true,
        ]);

        Book::create([
            'title' => 'Nuovo romanzo',
            'slug' => Str::slug('Nuovo romanzo'),
            'status' => 'coming',
            'genre' => 'Narrativa',
            'description' => 'Un nuovo progetto letterario in arrivo. Presto saranno disponibili dettagli e data ufficiale.',
            'synopsis' => 'Un romanzo in lavorazione su una scelta rimandata troppo a lungo e sulle conseguenze gentili, ma inevitabili, del tornare indietro.',
            'excerpt' => 'Ogni partenza cominciava sempre prima della valigia: in una frase detta male, in una porta chiusa piano, in una domenica troppo luminosa.',
            'reviews' => [
                ['quote' => 'Un progetto da seguire per il suo tono limpido e personale.', 'author' => 'Diario di lavorazione'],
            ],
            'cover' => 'images/books/libro-2.jpg',
            'amazon_url' => null,
            'is_featured' => true,
        ]);

        Book::create([
            'title' => 'Racconti brevi',
            'slug' => Str::slug('Racconti brevi'),
            'status' => 'writing',
            'genre' => 'Racconti',
            'description' => 'Una raccolta di testi, frammenti e racconti brevi legati al mondo emotivo e narrativo dell’autrice.',
            'synopsis' => 'Una raccolta di frammenti narrativi, scene brevi e personaggi incontrati di passaggio, uniti da uno sguardo attento alle emozioni più minute.',
            'excerpt' => 'Lei conservava i nomi delle persone come si conservano i fiori tra le pagine: non per fermarli, ma per ricordare il giorno esatto in cui erano esistiti.',
            'reviews' => [
                ['quote' => 'Frammenti che sembrano piccoli inizi, o finali lasciati aperti.', 'author' => 'Appunti di lettura'],
            ],
            'cover' => 'images/books/libro-3.jpg',
            'amazon_url' => null,
            'is_featured' => true,
        ]);

        Event::create([
            'title' => 'Incontro con i lettori',
            'category' => 'Presentazione libro',
            'description' => 'Presentazione del nuovo progetto editoriale e dialogo con il pubblico.',
            'event_date' => now()->addMonth(),
            'location' => 'Torino',
            'link' => null,
            'is_active' => true,
        ]);

        Event::create([
            'title' => 'Firmacopie in libreria',
            'category' => 'Firmacopie',
            'description' => 'Un momento dedicato a lettrici e lettori, con copie firmate e dediche.',
            'event_date' => now()->addMonths(2),
            'location' => 'Milano',
            'link' => null,
            'is_active' => true,
        ]);

        Post::create([
            'title' => 'Il valore delle parole lente',
            'category' => 'Scrittura',
            'slug' => Str::slug('Il valore delle parole lente'),
            'excerpt' => 'Una riflessione sul bisogno di fermarsi e ascoltare ciò che scriviamo.',
            'body' => 'Qui verrà inserito il testo completo dell’articolo.',
            'is_published' => true,
        ]);

        Post::create([
            'title' => 'Appunti da una pagina bianca',
            'category' => 'Diario',
            'slug' => Str::slug('Appunti da una pagina bianca'),
            'excerpt' => 'Quando una nuova storia nasce dal silenzio e da una domanda.',
            'body' => 'Qui verrà inserito il testo completo dell’articolo.',
            'is_published' => true,
        ]);

        Post::create([
            'title' => 'Personaggi che bussano alla porta',
            'category' => 'Ispirazione',
            'slug' => Str::slug('Personaggi che bussano alla porta'),
            'excerpt' => 'Come un personaggio può nascere da un dettaglio quotidiano.',
            'body' => 'Qui verrà inserito il testo completo dell’articolo.',
            'is_published' => true,
        ]);

        Testimonial::create([
            'quote' => 'Una scrittura intima, capace di trasformare i dettagli in memoria.',
            'author' => 'Lettura in anteprima',
            'context' => 'Romanzo',
            'is_active' => true,
        ]);

        Testimonial::create([
            'quote' => 'Ogni pagina sembra ascoltare prima di parlare.',
            'author' => 'Nota editoriale',
            'context' => 'Scrittura',
            'is_active' => true,
        ]);

        Testimonial::create([
            'quote' => 'Un tono delicato ma netto, con immagini che restano.',
            'author' => 'Lettrice beta',
            'context' => 'Racconti',
            'is_active' => true,
        ]);
    }
}
