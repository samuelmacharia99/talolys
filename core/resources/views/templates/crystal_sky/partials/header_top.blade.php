@php
    $contact = getContent('contact_us.content', true);
@endphp
<div class="header-top d-lg-block d-none">
    <div class="container">
        <div class="top-header-wrapper flex-between">
            <ul class="top-contact contact-list">
                <li class="contact-list__item">
                    <span class="contact-list__item-icon"><i class="fas fa-phone-alt"></i></span>
                    <a href="tel:{{ @$contact->data_values->contact_number }}" class="contact-list__link">
                        {{ @$contact->data_values->contact_number }}
                    </a>
                </li>
                <li class="contact-list__item">
                    <span class="contact-list__item-icon">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <a href="mailto:{{ @$contact->data_values->email_address }}" class="contact-list__link">
                        {{ @$contact->data_values->email_address }}
                    </a>
                </li>
                <li class="contact-list__item">
                    <span class="contact-list__item-icon"><i class="fas fa-map-marker-alt"></i></span>
                    <span class="contact-list__link">
                        {{ @$contact->data_values->contact_address }}
                    </span>
                </li>
            </ul>
            <ul class="top-button follow-social-list d-flex flex-wrap justify-content-between align-items-center">
                <span class="follow-social-list__text">@lang('Follow Us')</span>
                @include('Template::partials.social_link')
            </ul>
        </div>
    </div>
</div>
