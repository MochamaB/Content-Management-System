<style>
    .horizontal-timeline .items {
        border-top: 3px solid #e9ecef;
    }

    .horizontal-timeline .items .items-list {
        display: block;
        position: relative;
        text-align: center;
        padding-top: 70px;
        margin-right: 0;
    }

    .horizontal-timeline .items .items-list:before {
        content: "";
        position: absolute;
        height: 36px;
        border-right: 2px dashed #dee2e6;
        top: 0;
    }

    .horizontal-timeline .items .items-list .event-date {
        position: absolute;
        top: 36px;
        left: 0;
        right: 0;
        width: 75px;
        margin: 0 auto;
        font-size: 0.9rem;
        padding-top: 8px;
    }

    @media (min-width: 1140px) {
        .horizontal-timeline .items .items-list {
            display: inline-block;
            width: 24%;
            padding-top: 45px;
        }

        .horizontal-timeline .items .items-list .event-date {
            top: -40px;
        }
    }
</style>
<div class="container-fluid py-5">

  <div class="row">
    <div class="col-lg-12">

      <div class="horizontal-timeline">

        <ul class="list-inline items">
          <li class="list-inline-item items-list">
            <div class="px-4">
              <div class="event-date badge bg-info">2 June</div>
              <h5 class="pt-2">Event One</h5>
              <p class="text-muted">It will be as simple as occidental in fact it will be Occidental Cambridge
                friend</p>
              <div>
                <a href="#" data-mdb-ripple-init class="btn btn-primary btn-sm">Read more</a>
              </div>
            </div>
          </li>
          <li class="list-inline-item items-list">
            <div class="px-4">
              <div class="event-date badge bg-success">5 June</div>
              <h5 class="pt-2">Event Two</h5>
              <p class="text-muted">Everyone realizes why a new common language one could refuse translators.
              </p>
              <div>
                <a href="#" data-mdb-ripple-init class="btn btn-primary btn-sm">Read more</a>
              </div>
            </div>
          </li>
          <li class="list-inline-item items-list">
            <div class="px-4">
              <div class="event-date badge bg-danger">7 June</div>
              <h5 class="pt-2">Event Three</h5>
              <p class="text-muted">If several languages coalesce the grammar of the resulting simple and
                regular</p>
              <div>
                <a href="#" data-mdb-ripple-init class="btn btn-primary btn-sm">Read more</a>
              </div>
            </div>
          </li>
          <li class="list-inline-item items-list">
            <div class="px-4">
              <div class="event-date badge bg-warning">8 June</div>
              <h5 class="pt-2">Event Four</h5>
              <p class="text-muted">Languages only differ in their pronunciation and their most common words.
              </p>
              <div>
                <a href="#" data-mdb-ripple-init class="btn btn-primary btn-sm">Read more</a>
              </div>
            </div>
          </li>
          
        </ul>

      </div>

    </div>
  </div>

</div>