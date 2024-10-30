@extends('admin.templateAdmin')

@section('title', 'Manage Quiz')

@section('content')
<div class="content-area" style="max-width: 800px; margin: auto;">
    <div class="text-center mb-4">
        <h1 class="display-5">Manage Quiz</h1>
        <hr class="my-4">
    </div>

    <!-- Description -->
    <div class="alert alert-info" role="alert">
        Below is the list of available questions for the quiz. You can toggle the questions to make them active or inactive, and select the correct answer for each question.
    </div>

    <!-- Questions List Container with white background and scrollable -->
    <div id="questionList" style="background-color: white; padding: 20px; border: 1px solid #ddd; border-radius: 8px; max-height: 500px; overflow-y: auto;">
        <!-- Dynamic questions will be inserted here -->
    </div>

    <!-- Save Button -->
    <div class="text-center mt-4">
        <button class="btn btn-primary" onclick="saveQuizSettings()">Save Quiz Settings</button>
    </div>

    <!-- Output Area for Frontend Testing -->
    <div class="mt-4">
        <h4>JSON Output (Simulated Save)</h4>
        <pre id="jsonOutput"></pre>
    </div>
</div>
@endsection

@section('scripts')
        <script>
            // Simulated questions (as if they were extracted from a Word document)
            const questions = [
                {
    id: 1, 
    text: "¿Cuál es el objetivo principal del entrenamiento de seguridad en el manejo de materiales químicos en los laboratorios del RUM?", 
    options: [
        "a) Aprender a manejar residuos radiactivos", 
        "b) Cumplir con las normas de la universidad", 
        "c) Garantizar la seguridad personal y el cumplimiento de regulaciones locales y federales", 
        "d) Aprender a usar equipos de laboratorio avanzados"
    ],
    correct: "c) Garantizar la seguridad personal y el cumplimiento de regulaciones locales y federales",
    active: true 
},
{ 
    id: 2, 
    text: "¿Qué normativa regula la exposición a químicos peligrosos en los laboratorios según OSHA?", 
    options: [
        "a) 29 CFR 1910.1200", 
        "b) Subparte K de la CFR 40 Parte 262", 
        "c) 29 CFR 1910.1450", 
        "d) Plan de Manejo de Laboratorio del RUM"
    ],
    correct: "c) 29 CFR 1910.1450",
    active: true 
},
{
    id: 3,
    text: "¿Qué se debe hacer si en un laboratorio se acumula el máximo de galones de materiales no deseados?",
    options: [
        "a) Los materiales deben ser etiquetados inmediatamente",
        "b) Los materiales deben ser retirados en un plazo de 10 días",
        "c) Se deben transferir a otro laboratorio",
        "d) Deben ser almacenados por un máximo de 6 meses"
    ],
    correct: "b) Los materiales deben ser retirados en un plazo de 10 días",
    active: true
},
{
    id: 4,
    text: "¿Cuál de las siguientes es una de las mejores prácticas recomendadas en el Plan de Manejo de Laboratorio?",
    options: [
        "a) Almacenar todos los productos químicos juntos sin importar su compatibilidad",
        "b) No etiquetar los productos químicos para ahorrar tiempo",
        "c) Utilizar métodos adecuados para el almacenamiento y la segregación de sustancias químicas peligrosas",
        "d) Usar cualquier contenedor disponible para almacenar productos químicos"
    ],
    correct: "c) Utilizar métodos adecuados para el almacenamiento y la segregación de sustancias químicas peligrosas",
    active: true
},
{
    id: 5,
    text: "¿Qué información debe incluirse en el etiquetado de los materiales no deseados?",
    options: [
        "a) Solo el nombre del material",
        "b) Nombre del material, fecha de acumulación y cantidad",
        "c) Solo la cantidad del material",
        "d) Fecha de acumulación y responsable del material"
    ],
    correct: "b) Nombre del material, fecha de acumulación y cantidad",
    active: true
},
{
    id: 6,
    text: "¿Cuál es el límite de tiempo que los materiales no deseados pueden permanecer en un laboratorio antes de ser retirados?",
    options: [
        "a) 1 mes",
        "b) 6 meses",
        "c) 3 meses",
        "d) 12 meses"
    ],
    correct: "b) 6 meses",
    active: true
},
{
    id: 7,
    text: "¿Qué volumen máximo de materiales no deseados puede acumularse en un laboratorio antes de que deban ser retirados en 10 días?",
    options: [
        "a) 10 galones",
        "b) 55 galones",
        "c) 25 galones",
        "d) 5 galones"
    ],
    correct: "b) 55 galones",
    active: true
},
{
    id: 8,
    text: "¿Qué recomendación se debe seguir al almacenar sustancias corrosivas o especialmente peligrosas (PHS)?",
    options: [
        "a) Almacenarlas por encima del nivel de los ojos",
        "b) Almacenarlas en estantes montados en la pared",
        "c) Almacenarlas por debajo del nivel de los ojos",
        "d) Almacenarlas cerca de fuentes de calor"
    ],
    correct: "c) Almacenarlas por debajo del nivel de los ojos",
    active: true
},
{
    id: 9,
    text: "¿Qué se encontró durante la inspección de la EPA en marzo de 2023 relacionado con el almacenamiento de materiales?",
    options: [
        "a) Todos los materiales estaban correctamente etiquetados",
        "b) Los materiales estaban organizados de manera ejemplar",
        "c) Algunos materiales estaban almacenados de manera incompatible y en contenedores en mal estado",
        "d) No se encontraron problemas significativos"
    ],
    correct: "c) Algunos materiales estaban almacenados de manera incompatible y en contenedores en mal estado",
    active: true
},
{
    id: 10,
    text: "¿Cuál es la principal razón para segregar los materiales químicos en el laboratorio?",
    options: [
        "a) Facilitar la búsqueda de productos",
        "b) Evitar reacciones peligrosas entre materiales incompatibles",
        "c) Mejorar la estética del laboratorio",
        "d) Reducir la cantidad de inventario almacenado"
    ],
    correct: "b) Evitar reacciones peligrosas entre materiales incompatibles",
    active: true
},
{
    id: 11,
    text: "¿Cuál es el primer paso en el proceso de almacenamiento de productos químicos?",
    options: [
        "a) Segregar los productos por compatibilidad química",
        "b) Organizar los productos por orden alfabético",
        "c) Separar los productos por su estado físico (sólidos, líquidos, gases)",
        "d) Colocar los productos en contenedores secundarios"
    ],
    correct: "c) Separar los productos por su estado físico (sólidos, líquidos, gases)",
    active: true
},
{
    id: 12,
    text: "¿Qué representa el pictograma de corrosión en los materiales químicos?",
    options: [
        "a) Que la sustancia es inflamable",
        "b) Que puede causar quemaduras graves en la piel o daños a los metales",
        "c) Que es tóxica si se ingiere",
        "d) Que es un peligro para el medio ambiente"
    ],
    correct: "b) Que puede causar quemaduras graves en la piel o daños a los metales",
    active: true
},
{
id: 13,
text: "¿Cuál es la función principal de las Hojas de Datos de Seguridad (SDS)?",
options: [
    "a) Proporcionar una lista de precios de los productos químicos",
    "b) Informar sobre las propiedades, peligros y manejo seguro de los productos químicos",
    "c) Identificar el fabricante del producto",
    "d) Organizar los materiales en el laboratorio"
],
correct: "b) Informar sobre las propiedades, peligros y manejo seguro de los productos químicos",
active: true
},
{
id: 14,
text: "¿Qué información importante debe incluirse en una SDS relacionada con la exposición personal?",
options: [
    "a) Solo las propiedades físicas del producto",
    "b) Medidas de primeros auxilios, control de exposición y equipo de protección personal",
    "c) La historia del fabricante",
    "d) Normativas locales sobre transporte"
],
correct: "b) Medidas de primeros auxilios, control de exposición y equipo de protección personal",
active: true
},
{
id: 15,
text: "¿Qué pictograma alerta sobre el riesgo para el medio ambiente?",
options: [
    "a) El signo de exclamación",
    "b) El símbolo de un pez y un árbol",
    "c) El pictograma de toxicidad aguda",
    "d) El pictograma de explosivos"
],
correct: "b) El símbolo de un pez y un árbol",
active: true
},
{
id: 16,
text: "¿Cuál es el riesgo de almacenar oxidantes junto a materiales combustibles en el laboratorio?",
options: [
    "a) Ninguno, siempre que estén en diferentes contenedores",
    "b) Puede causar o intensificar incendios",
    "c) Pueden generar gases tóxicos",
    "d) Aumenta la presión en los contenedores"
],
correct: "b) Puede causar o intensificar incendios",
active: true
},
{
id: 17,
text: "¿Qué tipo de contenedores deben utilizarse para almacenar productos químicos que son propensos a derrames o fugas?",
options: [
    "a) Contenedores sellados herméticamente",
    "b) Contenedores secundarios",
    "c) Contenedores de vidrio grueso",
    "d) Bolsas plásticas gruesas"
],
correct: "b) Contenedores secundarios",
active: true
},
{
id: 18,
text: "¿Cuál es la distancia mínima recomendada desde el techo para almacenar químicos en laboratorios que NO tienen rociadores?",
options: [
    "a) 12 pulgadas",
    "b) 24 pulgadas",
    "c) 18 pulgadas",
    "d) 36 pulgadas"
],
correct: "b) 24 pulgadas",
active: true
},
{
id: 19,
text: "¿Qué se encontró como uno de los principales problemas durante la inspección de la EPA en 2023 relacionado con los materiales no deseados?",
options: [
    "a) Uso incorrecto de contenedores plásticos",
    "b) Etiquetas incorrectas, como “residuos” en lugar de “material no deseado”",
    "c) Volumen de materiales excedido en las campanas de extracción",
    "d) Faltaban hojas de datos de seguridad en los laboratorios"
],
correct: "b) Etiquetas incorrectas, como “residuos” en lugar de “material no deseado”",
active: true
},
{
id: 20,
text: "¿Por qué no es recomendable almacenar productos químicos dentro de las campanas extractoras?",
options: [
    "a) Porque reduce el espacio para realizar experimentos",
    "b) Porque compromete la eficacia de la campana para proteger al personal de gases y vapores peligrosos",
    "c) Porque puede sobrecargar el sistema de ventilación",
    "d) Porque es más difícil acceder a los productos químicos en una campana extractora"
],
correct: "b) Porque compromete la eficacia de la campana para proteger al personal de gases y vapores peligrosos",
active: true
},
{
id: 21,
text: "¿Qué método debe utilizarse para organizar productos químicos en lugar de orden alfabético?",
options: [
    "a) Por estado físico",
    "b) Por compatibilidad química",
    "c) Por volumen de contenedores",
    "d) Por temperatura de almacenamiento"
],
correct: "b) Por compatibilidad química",
active: true
},
{
id: 22,
text: "¿Qué ventaja ofrece el uso de contenedores secundarios al almacenar líquidos peligrosos?",
options: [
    "a) Facilita el transporte de los químicos",
    "b) Ayuda a contener derrames y minimizar el riesgo de fugas",
    "c) Permite almacenar más productos en menos espacio",
    "d) Mejora la identificación de los químicos almacenados"
],
correct: "b) Ayuda a contener derrames y minimizar el riesgo de fugas",
active: true
},
{
id: 23,
text: "¿Qué tipo de equipos deben usarse para almacenar materiales inflamables como líquidos peligrosos?",
options: [
    "a) Refrigeradores y congeladores estándar",
    "b) Refrigeradores y congeladores diseñados específicamente para almacenar materiales inflamables",
    "c) Contenedores herméticos sellados al vacío",
    "d) Estanterías ventiladas y cerradas"
],
correct: "b) Refrigeradores y congeladores diseñados específicamente para almacenar materiales inflamables",
active: true
}
];


    

// Load quiz questions from localStorage for managing settings
function loadQuizSettings() {
    const questions = JSON.parse(localStorage.getItem('quizSettings')) || [];
    const questionList = document.getElementById('questionList');
    questionList.innerHTML = ''; // Clear previous content

    questions.forEach((question, index) => {
        const optionsHtml = question.options.map((option, optIndex) => `
            <option value="${option}" ${option === question.correct ? 'selected' : ''}>
                ${optIndex + 1}. ${option}
            </option>
        `).join('');

        const questionItem = `
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input question-checkbox" type="checkbox" id="question${question.id}" ${question.active ? 'checked' : ''} onchange="checkSelectedQuestions()">
                    <label class="form-check-label" for="question${question.id}">
                        ${index + 1}. ${question.text}
                    </label>
                </div>
                <div class="mt-2">
                    <label for="correctAnswer${question.id}" class="form-label">Correct Answer:</label>
                    <select id="correctAnswer${question.id}" class="form-select">
                        ${optionsHtml}
                    </select>
                </div>
            </div>
        `;
        questionList.innerHTML += questionItem;
    });
}

// Function to check if exactly 15 questions are selected
function checkSelectedQuestions() {
    const selectedQuestions = document.querySelectorAll('.question-checkbox:checked');
    const saveButton = document.getElementById('saveSettingsBtn');
    saveButton.disabled = selectedQuestions.length !== 15;
}

// Save quiz settings to localStorage only if 15 questions are selected
function saveQuizSettings() {
    const questions = JSON.parse(localStorage.getItem('quizSettings')) || [];
    const selectedQuestions = document.querySelectorAll('.question-checkbox:checked');
    const saveButton = document.getElementById('saveSettingsBtn');
    
    // Ensure 15 questions are selected
    if (selectedQuestions.length === 15) {
        selectedQuestions.forEach(checkbox => {
            const questionId = checkbox.id.replace('question', '');
            const correctAnswerSelect = document.getElementById(`correctAnswer${questionId}`);
            const question = questions.find(q => q.id == questionId);
            
            // Update each selected question's active status and correct answer
            question.active = true;
            question.correct = correctAnswerSelect.value;
        });

        // Update JSON output and allow download
        localStorage.setItem('quizSettings', JSON.stringify(questions));
        document.getElementById('jsonOutput').textContent = JSON.stringify(questions, null, 2);

        const resultBlob = new Blob([JSON.stringify(questions, null, 2)], { type: 'application/json' });
        const downloadLink = document.createElement('a');
        downloadLink.href = URL.createObjectURL(resultBlob);
        downloadLink.download = 'quizSettings.json';
        downloadLink.click();
    } else {
        alert("Please select exactly 15 questions before saving.");
    }
}

// Load questions on page load
document.addEventListener('DOMContentLoaded', loadQuizSettings);

// Load the questions when the page loads
document.addEventListener('DOMContentLoaded', loadQuestions);
</script>
@endsection