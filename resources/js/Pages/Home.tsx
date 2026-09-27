import { Head } from '@inertiajs/react';
import { FeatureCallouts, Hero, NewsAndEvents, QuickAccess } from '../Components/public/HomeSections';
import { eventPreviews, newsPreviews } from '../fixtures/home';
import PublicLayout from '../Layouts/PublicLayout';

export default function Home() {
    return <PublicLayout><Head title="Mutoko Rural District Council"><meta name="description" content="Council services, public information and development updates from Mutoko Rural District Council." /></Head><Hero /><QuickAccess /><NewsAndEvents news={newsPreviews} events={eventPreviews} /><section className="council-intro" id="council-intro" aria-labelledby="council-intro-heading"><div className="container intro-grid"><div><p className="eyebrow">Your council</p><h2 id="council-intro-heading">Serving Mutoko communities</h2></div><p>This website is being prepared to make approved council information and public services easier to find. Published details will be added as they are verified.</p></div></section><FeatureCallouts /></PublicLayout>;
}
