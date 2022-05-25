<?php
/* 
GOOGLE'S RECOMMENDED TYPES:

Article
Book (limited access)
Breadcrumb
Carousel
Course
Dataset
EmployerAggregateRating
Estimated Salary
Event
Fact Check
FAQ
Home Activities
How-To
Image License
Job Posting
Job Training (beta)
Local Business
Logo
Movie
Podcast
Product
Q&A
Recipe
Review Snippet
Sitelinks Search Box
Software App
Speakable (beta)
Subscription and Paywalled Content
Video

////////////////////
// MOST RELEVANT //
//////////////////

ARTICLE:
<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "NewsArticle",
        "headline": "Article headline",
        "image": [
            "https://example.com/photos/1x1/photo.jpg",
            "https://example.com/photos/4x3/photo.jpg",
            "https://example.com/photos/16x9/photo.jpg"
        ],
        "datePublished": "2015-02-05T08:00:00+08:00",
        "dateModified": "2015-02-05T09:20:00+08:00"
    }
</script>

BREADCRUMBS:
<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [{
            "@type": "ListItem",
            "position": 1,
            "name": "Books",
            "item": "https://example.com/books"
        },
        {
            "@type": "ListItem",
            "position": 2,
            "name": "Science Fiction",
            "item": "https://example.com/books/sciencefiction"
        },
        {
            "@type": "ListItem",
            "position": 3,
            "name": "Award Winners"
        }]
    }
</script>

COURSE:
<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Course",
        "name": "Introduction to Computer Science and Programming",
        "description": "Introductory CS course laying out the basics.",
        "provider": {
            "@type": "Organization",
            "name": "University of Technology - Eureka",
            "sameAs": "http://www.ut-eureka.edu"
        }
    }
</script>

DEFINITION:
A series or unit of curriculum that contains lectures, lessons, or modules in a particular subject and/or topic.

TECHNICAL GUIDELINES: 
Each course must have valid name and provider properties. For example, the following naming practices are not valid:

 - Promotional phrases: "Best school in the world"
 - Prices in course titles: "Learn ukulele - only $30!"
 - Using something other than a course for a title, such as: "Make money fast with this class!"
 - Discounts or purchase opportunties, such as: "Leaders in their fields share their secrets — 25% off!"

FAQ:
<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "FAQPage",
        "mainEntity": [
            {
                "@type": "Question",
                "name": "What is the return policy?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "<p>Most unopened items in new condition and returned within <strong>90 days</strong> will receive a refund or exchange. Some items have a modified return policy noted on the receipt or packing slip. Items that are opened or damaged or do not have a receipt may be denied a refund or exchange. Items purchased online or in-store may be returned to any store.</p><p>Online purchases may be returned via a major parcel carrier. <a href=http://example.com/returns> Click here </a> to initiate a return.</p>"
                }
            }, 
            {
                "@type": "Question",
                "name": "How long does it take to process a refund?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "We will reimburse you for returned items in the same way you paid for them. For example, any amounts deducted from a gift card will be credited back to a gift card. For returns by mail, once we receive your return, we will process it within 4–5 business days. It may take up to 7 days after we process the return to reflect in your account, depending on your financial institution's processing time."
                }
            }, 
            {
                "@type": "Question",
                "name": "What is the policy for late/non-delivery of items ordered online?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "<p>Our local teams work diligently to make sure that your order arrives on time, within our normaldelivery hours of 9AM to 8PM in the recipient's time zone. During  busy holiday periods like Christmas, Valentine's and Mother's Day, we may extend our delivery hours before 9AM and after 8PM to ensure that all gifts are delivered on time. If for any reason your gift does not arrive on time, our dedicated Customer Service agents will do everything they can to help successfully resolve your issue.</p><p><a href=https://example.com/orders/>Click here</a> to complete the form with your order-related question(s).</p>"
                }
            }, 
            {
                "@type": "Question",
                "name": "When will my credit card be charged?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "We'll attempt to securely charge your credit card at the point of purchase online. If there's a problem, you'll be notified on the spot and prompted to use another card. Once we receive verification of sufficient funds, your payment will be completed and transferred securely to us. Your account will be charged in 24 to 48 hours."
                }
            }, 
            {
                "@type": "Question",
                "name": "Will I be charged sales tax for online orders?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Local and State sales tax will be collected if your recipient's mailing address is in: <ul><li>Arizona</li><li>California</li><li>Colorado</li></ul>"
                }
            }
        ]
    }
</script>

HOW-TO:
<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "HowTo",
        "name": "How to tile a kitchen backsplash",
        "description": "Any kitchen can be much more vibrant with a great tile backsplash. This guide will help you install one with beautiful results, like our example kitchen seen here.",
        "image": {
            "@type": "ImageObject",
            "url": "https://example.com/photos/1x1/photo.jpg",
            "height": "406",
            "width": "305"
        },
        "estimatedCost": {
            "@type": "MonetaryAmount",
            "currency": "USD",
            "value": "100"
        },
        "supply": [
            {
                "@type": "HowToSupply",
                "name": "tiles"
            }, 
            {
                "@type": "HowToSupply",
                "name": "thin-set mortar"
            }, 
            {
                "@type": "HowToSupply",
                "name": "tile grout"
            }, 
            {
                "@type": "HowToSupply",
                "name": "grout sealer"
            }
        ],
        "tool": [
            {
                "@type": "HowToTool",
                "name": "notched trowel"
            }, {
                "@type": "HowToTool",
                "name": "bucket"
            },{
                "@type": "HowToTool",
                "name": "large sponge"
            }
        ],
        "step": [
            {
                "@type": "HowToStep",
                "url": "https://example.com/kitchen#step1",
                "name": "Prepare the surfaces",
                "itemListElement": [
                    {
                        "@type": "HowToDirection",
                        "text": "Turn off the power to the kitchen and then remove everything that is on the wall, such as outlet covers, switchplates, and any other item in the area that is to be tiled."
                    }, 
                    {
                        "@type": "HowToDirection",
                        "text": "Then clean the surface thoroughly to remove any grease or other debris and tape off the area."
                    }
                ],
                "image": {
                    "@type": "ImageObject",
                    "url": "https://example.com/photos/1x1/photo-step1.jpg",
                    "height": "406",
                    "width": "305"
                }
            }, 
            {
                "@type": "HowToStep",
                "name": "Plan your layout",
                "url": "https://example.com/kitchen#step2",
                "itemListElement": [
                    {
                        "@type": "HowToTip",
                        "text": "The creases created up until this point will be guiding lines for creating the four walls of your planter box."
                    }, 
                    {
                        "@type": "HowToDirection",
                        "text": "Lift one side at a 90-degree angle, and fold it in place so that the point on the paper matches the other two points already in the center."
                    }, 
                    {
                        "@type": "HowToDirection",
                        "text": "Repeat on the other side."
                    }
                ],
                "image": {
                    "@type": "ImageObject",
                    "url": "https://example.com/photos/1x1/photo-step2.jpg",
                    "height": "406",
                    "width": "305"
                }
            }, 
            {
                "@type": "HowToStep",
                "name": "Prepare your and apply mortar (or choose adhesive tile)",
                "url": "https://example.com/kitchen#step3",
                "itemListElement": [
                    {
                        "@type": "HowToDirection",
                        "text": "Follow the instructions on your thin-set mortar to determine the right amount of water to fill in your bucket. Once done, add the powder gradually and make sure it is thoroughly mixed."
                    }, 
                    {
                        "@type": "HowToDirection",
                        "text": "Once mixed, let it stand for a few minutes before mixing it again. This time do not add more water. Double check your thin-set mortar instructions to make sure the consistency is right."
                    }, 
                    {
                        "@type": "HowToDirection",
                        "text": "Spread the mortar on a small section of the wall with a trowel."
                    }, 
                    {
                        "@type": "HowToTip",
                        "text": "Thinset and other adhesives set quickly so make sure to work in a small area."
                    }, 
                    {
                        "@type": "HowToDirection",
                        "text": "Once it's applied, comb over it with a notched trowel."
                    }
                ],
                "image": {
                    "@type": "ImageObject",
                    "url": "https://example.com/photos/1x1/photo-step3.jpg",
                    "height": "406",
                    "width": "305"
                }
            }, 
            {
                "@type": "HowToStep",
                "name": "Add your tile to the wall",
                "url": "https://example.com/kitchen#step4",
                "itemListElement": [
                    {
                        "@type": "HowToDirection",
                        "text": "Place the tile sheets along the wall, making sure to add spacers so the tiles remain lined up."
                    }, 
                    {
                        "@type": "HowToDirection",
                        "text": "Press the first piece of tile into the wall with a little twist, leaving a small (usually one-eight inch) gap at the countertop to account for expansion. use a rubber float to press the tile and ensure it sets in the adhesive."
                    }, 
                    {
                        "@type": "HowToDirection",
                        "text": "Repeat the mortar and tiling until your wall is completely tiled, Working in small sections."
                    }
                ],
                "image": {
                    "@type": "ImageObject",
                    "url": "https://example.com/photos/1x1/photo-step4.jpg",
                    "height": "406",
                    "width": "305"
                }
            }, 
            {
                "@type": "HowToStep",
                "name": "Apply the grout",
                "url": "https://example.com/kitchen#step5",
                "itemListElement": [
                    {
                        "@type": "HowToDirection",
                        "text": "Allow the thin-set mortar to set. This usually takes about 12 hours. Don't mix the grout before the mortar is set, because you don't want the grout to dry out!"
                    },
                    {
                        "@type": "HowToDirection",
                        "text": "To apply, cover the area thoroughly with grout and make sure you fill all the joints by spreading it across the tiles vertically, horizontally, and diagonally. Then fill any remaining voids with grout."
                    }, 
                    {
                        "@type": "HowToDirection",
                        "text": "Then, with a moist sponge, sponge away the excess grout and then wipe clean with a towel. For easier maintenance in the future, think about applying a grout sealer."
                    }
                ],
                "image": {
                    "@type": "ImageObject",
                    "url": "https://example.com/photos/1x1/photo-step5.jpg",
                    "height": "406",
                    "width": "305"
                }
            }
        ],
        "totalTime": "P2D"
    }
</script>

LOCAL BUSINESS:
<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Restaurant",
        "image": [
            "https://example.com/photos/1x1/photo.jpg",
            "https://example.com/photos/4x3/photo.jpg",
            "https://example.com/photos/16x9/photo.jpg"
        ],
        "name": "Dave's Steak House",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "148 W 51st St",
            "addressLocality": "New York",
            "addressRegion": "NY",
            "postalCode": "10019",
            "addressCountry": "US"
        },
        "review": 
        {
            "@type": "Review",
            "reviewRating": {
                "@type": "Rating",
                "ratingValue": "4",
                "bestRating": "5"
            },
            "author": {
                "@type": "Person",
                "name": "Lillian Ruiz"
            }
        },
        "geo": {
            "@type": "GeoCoordinates",
            "latitude": 40.761293,
            "longitude": -73.982294
        },
        "url": "http://www.example.com/restaurant-locations/manhattan",
        "telephone": "+12122459600",
        "servesCuisine": "American",
        "priceRange": "$$$",
        "openingHoursSpecification": [
            {
                "@type": "OpeningHoursSpecification",
                "dayOfWeek": [
                    "Monday",
                    "Tuesday"
                ],
                "opens": "11:30",
                "closes": "22:00"
            },
            {
                "@type": "OpeningHoursSpecification",
                "dayOfWeek": [
                    "Wednesday",
                    "Thursday",
                    "Friday"
                ],
                "opens": "11:30",
                "closes": "23:00"
            },
            {
                "@type": "OpeningHoursSpecification",
                "dayOfWeek": "Saturday",
                "opens": "16:00",
                "closes": "23:00"
            },
            {
                "@type": "OpeningHoursSpecification",
                "dayOfWeek": "Sunday",
                "opens": "16:00",
                "closes": "22:00"
            }
        ],
        "menu": "http://www.example.com/menu",
        "acceptsReservations": "True"
    }
</script>

LOGO:
<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "url": "http://www.example.com",
        "logo": "http://www.example.com/images/logo.png"
    }
</script>

PRODUCT (SINGLE):
<script type="application/ld+json">
    {
        "@context": "https://schema.org/",
        "@type": "Product",
        "name": "Executive Anvil",
        "image": [
            "https://example.com/photos/1x1/photo.jpg",
            "https://example.com/photos/4x3/photo.jpg",
            "https://example.com/photos/16x9/photo.jpg"
        ],
        "description": "Sleeker than ACME's Classic Anvil, the Executive Anvil is perfect for the business traveler looking for something to drop from a height.",
        "sku": "0446310786",
        "mpn": "925872",
        "brand": {
            "@type": "Brand",
            "name": "ACME"
        },
        "review": {
            "@type": "Review",
            "reviewRating": {
                "@type": "Rating",
                "ratingValue": "4",
                "bestRating": "5"
            },
            "author": {
                "@type": "Person",
                "name": "Fred Benson"
            }
        },
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "4.4",
            "reviewCount": "89"
        },
        "offers": {
            "@type": "Offer",
            "url": "https://example.com/anvil",
            "priceCurrency": "USD",
            "price": "119.99",
            "priceValidUntil": "2020-11-20",
            "itemCondition": "https://schema.org/UsedCondition",
            "availability": "https://schema.org/InStock"
        }
    }
</script>

REVIEW SNIPPET:
<script type="application/ld+json">
    {
        "@context": "https://schema.org/",
        "@type": "Product",
        "brand": {
            "@type": "Brand",
            "name": "Penguin Books"
        },
        "description": "The Catcher in the Rye is a classic coming-of-age story: an story of teenage alienation, capturing the human need for connection and the bewildering sense of loss as we leave childhood behind.",
        "sku": "9780241984758",
        "mpn": "925872",
        "image": "http://www.example.com/catcher-in-the-rye-book-cover.jpg",
        "name": "The Catcher in the Rye",
        "review": [
            {
                "@type": "Review",
                "reviewRating": {
                    "@type": "Rating",
                    "ratingValue": "5"
                },
                "author": {
                    "@type": "Person",
                    "name": "John Doe"
                },
                "reviewBody": "I really enjoyed this book. It captures the essential challenge people face as they try make sense of their lives and grow to adulthood."
            },
            {
                "@type": "Review",
                "reviewRating": {
                    "@type": "Rating",
                    "ratingValue": "1"
                },
                "author": {
                    "@type": "Person",
                    "name": "Jane Doe"
                },
                "reviewBody": "I really didn't care for this book."
            }
        ],
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "88",
            "bestRating": "100",
            "ratingCount": "20"
        },
        "offers": {
            "@type": "Offer",
            "url": "https://example.com/offers/catcher-in-the-rye",
            "priceCurrency": "USD",
            "price": "5.99",
            "priceValidUntil": "2020-11-05",
            "itemCondition": "https://schema.org/UsedCondition",
            "availability": "https://schema.org/InStock",
            "seller": {
                "@type": "Organization",
                "name": "eBay"
            }
        }
    }
</script>

SITELINKS SEARCH BOX (NESTED IN WEBSITE TYPE):
<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "url": "https://www.example.com/",
        "potentialAction": [
            {
                "@type": "SearchAction",
                "target": {
                    "@type": "EntryPoint",
                    "urlTemplate": "https://query.example.com/search?s={search_term_string}"
                },
                "query-input": "required name=search_term_string"
            },
            {
                "@type": "SearchAction",
                "target": {
                    "@type": "EntryPoint",
                    "urlTemplate": "android-app://com.example/https/query.example.com/search/?s={search_term_string}"
                },
                "query-input": "required name=search_term_string"
            }
        ]
    }
</script>

VIDEO:
<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "VideoObject",
        "name": "Introducing the self-driving bicycle in the Netherlands",
        "description": "This spring, Google is introducing the self-driving bicycle in Amsterdam, the world's premier cycling city. The Dutch cycle more than any other nation in the world, almost 900 kilometres per year per person, amounting to over 15 billion kilometres annually. The self-driving bicycle enables safe navigation through the city for Amsterdam residents, and furthers Google's ambition to improve urban mobility with technology. Google Netherlands takes enormous pride in the fact that a Dutch team worked on this innovation that will have great impact in their home country.",
        "thumbnailUrl": [
            "https://example.com/photos/1x1/photo.jpg",
            "https://example.com/photos/4x3/photo.jpg",
            "https://example.com/photos/16x9/photo.jpg"
        ],
        "uploadDate": "2016-03-31T08:00:00+08:00",
        "duration": "PT1M54S",
        "contentUrl": "https://www.example.com/video/123/file.mp4",
        "embedUrl": "https://www.example.com/embed/123",
        "interactionStatistic": {
            "@type": "InteractionCounter",
            "interactionType": { 
                "@type": "WatchAction" 
            },
            "userInteractionCount": 5647018
        },
        "regionsAllowed": "US,NL",
        "potentialAction" : {
            "@type": "SeekToAction",
            "target": "https://video.example.com/watch/videoID?t={seek_to_second_number}",
            "startOffset-input": "required name=seek_to_second_number"
        }
    }
</script>
*/