<?php

include '../connection.php';
include 'getUserDetails.php';

if (!isset($_SESSION["userlogin"])) {
	header("Location: ../login.php");
	exit();
}

$depart = false;
$vessel_id = '';
$vessel_name = '';
$catch_id = '';
$status = '';
$owner_id;

if (isset($_SESSION['id'])) {
	$owner_id = $_SESSION['id'];

	$sql = "
        SELECT catch_id, v.vessel_id, v.vessel_name, o.status 
        FROM tbl_catch_report cr
        JOIN tbl_vessel v ON cr.vessel_id = v.vessel_id
        JOIN tbl_owner o ON v.owner_id = o.owner_id
        WHERE o.owner_id = '$owner_id' 
        ORDER BY cr.depart_date DESC
        LIMIT 1
    ";

	$result = mysqli_query($conn, $sql);

	if ($result && mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_assoc($result);
		$catch_id = $row['catch_id'];
		$vessel_id = $row['vessel_id'];
		$vessel_name = $row['vessel_name'];
		$status = $row['status'];
		$depart = true;
	}
}
$load = isset($_SESSION['message']) || isset($_SESSION['error']);

$sell_id;
$hasCart = false;

$sqlCart = "SELECT sell_id 
        FROM tbl_sell
        WHERE status = 'On Cart'
        LIMIT 1";

$resultCart = mysqli_query($conn, $sqlCart);

if ($resultCart && mysqli_num_rows($resultCart) > 0) {
	$row = mysqli_fetch_assoc($resultCart);
	$sell_id = $row['sell_id'];
	$hasCart = true;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- Boxicons -->
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<!-- My CSS -->
	<link rel="stylesheet" href="style.css">
	<link rel="stylesheet" href="settings.css">
	<link rel="stylesheet" href="transaction.css">
	<link rel="stylesheet" href="catchdata.css">
	<link rel="stylesheet" href="buyerdata.css">
	<link rel="stylesheet" href="pricing.css">
	<link rel="stylesheet" href="vessels.css">
	<link rel="stylesheet" href="selling.css">
	<link rel="stylesheet" href="dashboard.css">
	<link rel="stylesheet" href="usermanager.css">
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.5/dist/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="sidebar.js"></script>

	<title>Fish Catch (<?php echo $whoLogIn; ?>)</title>
</head>

<body>

	<!-- Logout Confirmation Modal -->
	<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span>&times;</span>
					</button>
				</div>
				<div class="modal-body">
					Are you sure you want to logout?
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
					<a href="logout.php" class="btn btn-danger">Logout</a>
				</div>
			</div>
		</div>
	</div>
	<!-- Modal for Setting Price -->
	<div class="modal fade" id="setPriceModal" tabindex="-1" role="dialog" aria-labelledby="setPriceModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="setPriceModalLabel">Set Price for Fish</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form action="setPrice.php" method="post">
					<div class="modal-body">
						<div class="form-group">
							<label for="fishName">Fish Name</label>
							<input type="text" class="form-control" name="fish_name" id="fishName" required>
							<input type="hidden" name="fish_id" id="fishId">
							<input type="hidden" name="catch_id_add" id="catch_id_add">
						</div>
						<div class="form-row">
							<div class="form-group col-md-6">
								<label for="fishPrice">Price</label>
								<input type="number" class="form-control" name="fish_price" id="fishPrice" placeholder="Enter price" min="0" required>
							</div>
							<div class="form-group col-md-6">
								<label for="fishQuantity">Quantity</label>
								<input type="number" class="form-control" name="fish_quantity" id="fishQuantity" required>
							</div>
						</div>
						<div class="form-group">
							<label for="unitSelect">Unit</label>
							<select class="form-control" id="unitSelect" name="fish_unit" required>
								<option value="Kilo">Kilo</option>
								<option value="Box">Box</option>
								<option value="Banyera">Banyera</option>
							</select>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
						<button type="submit" class="btn btn-primary" id="setPriceBtn" name="setPrice">Set Price</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- Modal for Editing Price -->
	<div class="modal fade" id="editPriceModal" tabindex="-1" role="dialog" aria-labelledby="editPriceModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="editPriceModalLabel">Edit Price</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="editPriceForm" action="updatePrice.php" method="post">
						<div class="form-group">
							<input type="hidden" class="form-control" id="fishIdEdit" name="fish_id">
							<input type="hidden" class="form-control" id="catchIDEdit" name="catchIDEdit">
						</div>
						<div class="form-group">
							<label for="fishName">Fish Name</label>
							<input type="text" class="form-control" id="fishNameEdit" name="fish_name" readonly>
						</div>
						<div class="form-group">
							<label for="fishUnit">Unit</label>
							<input type="text" class="form-control" id="fishUnitEdit" name="unit" required>
						</div>
						<div class="form-group">
							<label for="fishQuantity">Quantity</label>
							<input type="number" class="form-control" id="fishQuantityEdit" name="quantity" required>
						</div>
						<div class="form-group">
							<label for="fishPrice">Price</label>
							<input type="number" class="form-control" id="fishPriceEdit" name="price" step="0.01" required>
						</div>
						<div class="form-group">
							<button type="submit" class="btn btn-primary">Update Price</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<section id="sidebar">
		<a href="#" class="brand">
			<i class='bx bxl-docker'></i>
			<span class="text">Fish Catch</span>
		</a>
		<ul class="side-menu top">
			<?php if ($_SESSION["userlogin"] == "admin") { ?>
				<li>
					<a href="javascript:void(0);" onclick="sidebarClick('dashboard')">
						<i class='bx bxs-dashboard'></i>
						<span class="text">Dashboard</span>
					</a>
				</li>
				<li>
					<a href="javascript:void(0);" onclick="sidebarClick('selling')">
						<i class='bx bx-money'></i>
						<span class="text">Sell</span>
					</a>
				</li>
				<li>
					<a href="javascript:void(0);" onclick="sidebarClick('vessels')">
						<i class='bx bxs-tachometer'></i>
						<span class="text">Vessels</span>
					</a>
				</li>
				<li>
					<a href="javascript:void(0);" onclick="sidebarClick('pricing')">
						<i class='bx bxs-purchase-tag'></i>
						<span class="text">Pricing</span>
					</a>
				</li>

				<li>
					<a href="javascript:void(0);" onclick="sidebarClick('usermanager')">
						<i class='bx bxs-purchase-tag'></i>
						<span class="text">Owner Manager</span>
					</a>
				</li>
			<?php } else { ?>
				<li>
					<a href="javascript:void(0);" onclick="sidebarClick('transaction')">
						<i class='bx bxs-store'></i>
						<span class="text">Departure and Return</span>
					</a>
				</li>
				<li>
					<a href="javascript:void(0);" onclick="sidebarClick('catchData')">
						<i class='bx bx-list-ul'></i>
						<span class="text">Fish Catch Data</span>
					</a>
				</li>
				<li>
					<a href="javascript:void(0);" onclick="sidebarClick('catchReport')">
						<i class='bx bxs-report'></i>
						<span class="text">Buyer Data</span>
					</a>
				</li>
			<?php } ?>

			<li>
				<a href="javascript:void(0);" onclick="sidebarClick('settings')">
					<i class='bx bxs-cog'></i>
					<span class="text">Settings</span>
				</a>
			</li>
		</ul>

		<ul class="side-menu">
			<li>
				<a href="#" class="logout" data-toggle="modal" data-target="#logoutModal">
					<i class='bx bxs-log-out-circle'></i>
					<span class="text">Logout</span>
				</a>
			</li>
		</ul>
	</section>

	<script>
		window.onload = function() {
			<?php if (isset($_SESSION['redirectTo'])): ?>
				sidebarClick('<?php echo htmlspecialchars($_SESSION['redirectTo']); ?>');
				unset($_SESSION['redirectTo']);
			<?php elseif ($_SESSION["userlogin"] == "admin"): ?>
				sidebarClick('dashboard');
			<?php else: ?>
				sidebarClick('transaction');
			<?php endif; ?>
		};
	</script>

	<!-- vessel pricing -->
	<script>
		$(document).ready(function() {
			$('#vesselSelectPricing').on('change', function() {
				var ownerId = $(this).val();
				if (ownerId) {
					loadPage(1, ownerId);
				} else {
					$('#catch-data-body').empty();
					$('#selectCatchID').empty();
					$('#selectCatchID').append('<option value="" disabled>Select Catch ID</option>');
				}
			});

			function loadPage(page, ownerId) {
				$.ajax({
					url: 'getFishCatchData.php',
					type: 'GET',
					data: {
						owner_id: ownerId,
						page: page
					},
					success: function(response) {
						var data = JSON.parse(response);
						var tbody = $('#catch-data-body');
						tbody.empty();

						if (data.data.length > 0) {
							data.data.forEach(function(catchItem) {
								var row = '<tr>' +
									'<td>' + catchItem.catch_id + '</td>' +
									'<td>' + catchItem.vessel_name + '</td>' +
									'<td>' + catchItem.depart_date + '</td>' +
									'<td>' + catchItem.return_date + '</td>' +
									'</tr>';
								tbody.append(row);
							});

							var catchDropdown = $('#selectCatchID');
							catchDropdown.empty();
							catchDropdown.append('<option value="" disabled selected>Select Catch ID</option>');

							data.data.forEach(function(catchItem) {
								catchDropdown.append('<option value="' + catchItem.catch_id + '">' + catchItem.catch_id + '</option>');
							});

							generatePaginationControls(data.totalPages, data.currentPage, ownerId);
						} else {
							tbody.append('<tr><td colspan="4">No catch data available for this owner.</td></tr>');
							$('#selectCatchID').empty();
							$('#selectCatchID').append('<option value="" disabled>No Catch IDs available</option>');
							$('#pagination-controls').empty();
						}
					},
					error: function() {
						alert('Error fetching data.');
					}
				});
			}

			function generatePaginationControls(totalPages, currentPage, ownerId) {
				var paginationControls = $('#pagination-controls');
				paginationControls.empty();

				if (totalPages > 1) {
					var paginationHtml = '<ul class="pagination justify-content-center">';

					paginationHtml += '<li class="page-item ' + (currentPage === 1 ? 'disabled' : '') + '">';
					paginationHtml += '<a class="page-link" href="#" data-page="' + (currentPage - 1) + '">&laquo;</a>';
					paginationHtml += '</li>';

					for (var i = 1; i <= totalPages; i++) {
						paginationHtml += '<li class="page-item ' + (i === currentPage ? 'active' : '') + '">';
						paginationHtml += '<a class="page-link" href="#" data-page="' + i + '">' + i + '</a>';
						paginationHtml += '</li>';
					}

					paginationHtml += '<li class="page-item ' + (currentPage === totalPages ? 'disabled' : '') + '">';
					paginationHtml += '<a class="page-link" href="#" data-page="' + (currentPage + 1) + '">&raquo;</a>';
					paginationHtml += '</li>';

					paginationHtml += '</ul>';
					paginationControls.html(paginationHtml);

					$('a.page-link').on('click', function(event) {
						event.preventDefault();
						var selectedPage = $(this).data('page');
						if (selectedPage && selectedPage !== currentPage) {
							loadPage(selectedPage, ownerId);
						}
					});
				}
			}

			$('#selectCatchID').on('change', function() {
				var catchId = $(this).val();

				if (catchId) {
					loadFishPage(1, catchId);
				} else {
					$('#fish-data-body').empty();
					$('#fish-pagination-controls').empty();
				}
			});

			function loadFishPage(page, catchId) {
				$.ajax({
					url: 'getFishData.php',
					type: 'GET',
					data: {
						catch_id: catchId,
						page: page
					},
					success: function(response) {
						var fishData = JSON.parse(response);
						var tbody = $('#fish-data-body');
						tbody.empty();
						if (fishData.data.length > 0) {
							fishData.data.forEach(function(fish) {
								var actionButton = '';

								if (fish.price === 'Not yet set' || fish.quantity === 'Not yet set' || fish.unit === 'Not yet set') {
									actionButton = '<a href="#" class="btn btn-primary" ' +
										'onclick="openModal(' + fish.catched_fish_id + ', \'' + fish.fish_name.replace(/'/g, "\\'") + '\', ' + fish.catch_id + ')">Set Price</a>';
								} else {
									actionButton = '<a href="#" class="btn btn-warning" ' +
										'onclick="openEditPriceModal(' + fish.catched_fish_id + ', \'' + fish.fish_name.replace(/'/g, "\\'") + '\', \'' + fish.unit + '\', ' + fish.quantity + ', ' + fish.price + ', ' + fish.catch_id + ')">Edit Price</a>';
								}

								var row = '<tr>' +
									'<td>' + fish.fish_name + '</td>' +
									'<td>' + fish.unit + '</td>' +
									'<td>' + fish.price + '</td>' +
									'<td>' + fish.quantity + '</td>' +
									'<td>' + actionButton + '</td>' +
									'</tr>';
								tbody.append(row);
							});

							generateFishPaginationControls(fishData.totalPages, fishData.currentPage, catchId);
						} else {
							tbody.append('<tr><td colspan="5">No fish data available for this catch ID.</td></tr>');
							$('#fish-pagination-controls').empty();
						}
					},
					error: function() {
						alert('Error fetching fish data.');
					}
				});
			}

			function generateFishPaginationControls(totalPages, currentPage, catchId) {
				var paginationControls = $('#fish-pagination-controls');
				paginationControls.empty();

				if (totalPages > 1) {
					var paginationHtml = '<ul class="pagination justify-content-center">';

					paginationHtml += '<li class="page-item ' + (currentPage === 1 ? 'disabled' : '') + '">';
					paginationHtml += '<a class="page-link" href="#" data-page="' + (currentPage - 1) + '">&laquo;</a>';
					paginationHtml += '</li>';

					for (var i = 1; i <= totalPages; i++) {
						paginationHtml += '<li class="page-item ' + (i === currentPage ? 'active' : '') + '">';
						paginationHtml += '<a class="page-link" href="#" data-page="' + i + '">' + i + '</a>';
						paginationHtml += '</li>';
					}

					paginationHtml += '<li class="page-item ' + (currentPage === totalPages ? 'disabled' : '') + '">';
					paginationHtml += '<a class="page-link" href="#" data-page="' + (currentPage + 1) + '">&raquo;</a>';
					paginationHtml += '</li>';

					paginationHtml += '</ul>';
					paginationControls.html(paginationHtml);

					$('a.page-link').on('click', function(event) {
						event.preventDefault();
						var selectedPage = $(this).data('page');
						if (selectedPage && selectedPage !== currentPage) {
							loadFishPage(selectedPage, catchId);
						}
					});
				}
			}

		});
	</script>

	<!-- modal na mo show para mo edit sa price sa fish-->
	<script>
		function openEditPriceModal(fishId, fishName, fishUnit, fishQuantity, fishPrice, catchIDEdit) {
			$('#fishIdEdit').val(fishId);
			$('#fishNameEdit').val(fishName);
			$('#fishUnitEdit').val(fishUnit);
			$('#fishQuantityEdit').val(fishQuantity);
			$('#fishPriceEdit').val(fishPrice);
			$('#catchIDEdit').val(catchIDEdit);

			var modal = new bootstrap.Modal(document.getElementById('editPriceModal'));
			modal.show();
		}
	</script>

	<!-- modal na mo show para mo set ug price sa fish-->
	<script>
		function openModal(fishId, fishName, catch_id) {
			$('#fishId').val(fishId);
			$('#catch_id_add').val(catch_id);
			$('#fishName').val(fishName);
			$('#fishPrice').val('');
			$('#fishQuantity').val('');

			// Open the modal
			var modal = new bootstrap.Modal(document.getElementById('setPriceModal'));
			modal.show();
		}
	</script>

	<!-- vessels na table pagination -->
	<script>
		$(document).ready(function() {
			const itemsPerPage = 10;

			function loadVesselData(page = 1) {
				$.ajax({
					url: 'vesselsPagination.php',
					type: 'GET',
					data: {
						page: page,
						limit: itemsPerPage
					},
					success: function(response) {
						if (response && response.tableData && response.paginationLinks) {
							$('#vesselTable tbody').html(response.tableData);
							$('#paginationLinksVessels').html(response.paginationLinks);
						}
					},
					error: function() {
						alert('Error loading data');
					}
				});
			}

			loadVesselData();

			$(document).on('click', '.page-link', function(e) {
				e.preventDefault();

				const page = $(this).data('page');
				loadVesselData(page);
			});
		});
	</script>

	<!-- search sa isda -->
	<script>
		$('#searchInput').on('keyup', function() {
			var searchTerm = $(this).val().toLowerCase();

			$('#fishItems .col-md-3').each(function() {
				var fishName = $(this).find('.card-title').text().toLowerCase();
				if (fishName.indexOf(searchTerm) !== -1) {
					$(this).show();
				} else {
					$(this).hide();
				}
			});
		});
	</script>

	<!-- Add Owner -->
	<div class="modal fade" id="addOwnerModal" tabindex="-1" role="dialog" aria-labelledby="addOwnerModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="addOwnerModalLabel">Add Owner</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form action="addOwner.php" method="post">
					<div class="modal-body">
						<div class="form-row">
							<div class="form-group col">
								<label for="firstName">First Name</label>
								<input type="text" class="form-control" id="firstName" name="firstname" placeholder="First Name" required>
							</div>
							<div class="form-group col">
								<label for="middleName">Middle Name</label>
								<input type="text" class="form-control" id="middleName" name="middlename" placeholder="Middle Name" required>
							</div>
							<div class="form-group col">
								<label for="lastName">Last Name</label>
								<input type="text" class="form-control" id="lastName" name="lastname" placeholder="Last Name" required>
							</div>
						</div>

						<div class="form-row">
							<div class="form-group col">
								<label for="phoneNumber">Phone Number</label>
								<input type="text" class="form-control" id="phoneNumber" name="phonenumber" placeholder="Phone Number" required>
							</div>
							<div class="form-group col">
								<label for="address">Address</label>
								<input type="text" class="form-control" id="address" name="address" placeholder="Address" required>
							</div>
						</div>

						<div class="form-row">
							<div class="form-group col">
								<label for="username">Username</label>
								<input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
							</div>
							<div class="form-group col">
								<label for="password">Password</label>
								<input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
						<button type="submit" class="btn btn-primary">Create Account</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Edit Owner Modal -->
	<div class="modal fade" id="editOwnerModal" tabindex="-1" role="dialog" aria-labelledby="editOwnerModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="editOwnerModalLabel">Edit Owner</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form action="editOwner.php" method="post">
					<div class="modal-body">
						<input type="hidden" id="editOwnerId" name="owner_id">
						<div class="form-row">
							<div class="form-group col">
								<label for="editFirstName">First Name</label>
								<input type="text" class="form-control" id="editFirstName" name="firstname" required>
							</div>
							<div class="form-group col">
								<label for="editMiddleName">Middle Name</label>
								<input type="text" class="form-control" id="editMiddleName" name="middlename" required>
							</div>
							<div class="form-group col">
								<label for="editLastName">Last Name</label>
								<input type="text" class="form-control" id="editLastName" name="lastname" required>
							</div>
						</div>

						<div class="form-row">
							<div class="form-group col">
								<label for="editPhoneNumber">Phone Number</label>
								<input type="text" class="form-control" id="editPhoneNumber" name="phonenumber" required>
							</div>
							<div class="form-group col">
								<label for="editAddress">Address</label>
								<input type="text" class="form-control" id="editAddress" name="address" required>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
						<button type="submit" class="btn btn-primary">Save Changes</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Delete Owner Modal -->
	<div class="modal fade" id="deleteOwnerModal" tabindex="-1" role="dialog" aria-labelledby="deleteOwnerModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="deleteOwnerModalLabel">Delete Owner</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form action="deleteOwner.php" method="post">
					<div class="modal-body">
						<input type="hidden" id="deleteOwnerId" name="owner_id">
						<p id="deleteOwnerMessage"></p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
						<button type="submit" class="btn btn-danger">Delete</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- mo select ug catch ID ang owner dayun mo gawas ang mga isda nga nakuha -->
	<script>
		$(document).ready(function() {
			$('#selectCatchIdOwner').on('change', function() {
				var catchId = $(this).val();
				if (catchId) {
					$.ajax({
						url: 'fetch_fish_data.php',
						type: 'GET',
						data: {
							catch_id: catchId
						},
						success: function(response) {
							var fishData = JSON.parse(response);
							var tableBody = $('#fish-list');
							tableBody.empty();

							if (fishData.length > 0) {
								var itemsPerPage = 10;
								var currentPage = 1;
								var totalPages = Math.ceil(fishData.length / itemsPerPage);

								function renderPage(page) {
									tableBody.empty();

									var startIndex = (page - 1) * itemsPerPage;
									var endIndex = startIndex + itemsPerPage;
									var pageData = fishData.slice(startIndex, endIndex);

									pageData.forEach(function(fish) {
										var row = '<tr>' +
											'<td>' + fish.fish_name + '</td>' +
											'<td>' + fish.price + '</td>' +
											'<td>' + fish.unit + '</td>' +
											'<td>' + fish.quantity + '</td>' +
											'</tr>';
										tableBody.append(row);
									});

									$('#pagination').html('');

									var prevButton = $('<li class="page-item"><a class="page-link" href="#">Previous</a></li>');
									if (page === 1) {
										prevButton.addClass('disabled');
									} else {
										prevButton.on('click', function(e) {
											e.preventDefault();
											renderPage(page - 1);
										});
									}
									$('#pagination').append(prevButton);

									for (var i = 1; i <= totalPages; i++) {
										var pageButton = $('<li class="page-item"><a class="page-link" href="#">' + i + '</a></li>');
										if (i === page) {
											pageButton.addClass('active');
										}
										pageButton.on('click', function(e) {
											e.preventDefault();
											var pageNum = parseInt($(this).text());
											renderPage(pageNum);
										});
										$('#pagination').append(pageButton);
									}

									var nextButton = $('<li class="page-item"><a class="page-link" href="#">Next</a></li>');
									if (page === totalPages) {
										nextButton.addClass('disabled');
									} else {
										nextButton.on('click', function(e) {
											e.preventDefault();
											renderPage(page + 1);
										});
									}
									$('#pagination').append(nextButton);
								}

								renderPage(currentPage);

							} else {
								tableBody.append('<tr><td colspan="4">No fish found for this Catch ID.</td></tr>');
							}
						}
					});
				}
			});
		});
	</script>

	<!-- sa pag edit sa vessel -->
	<script>
		$(document).ready(function() {
			$('#select_vessels_edit').change(function() {
				var vessel_id = $(this).val();

				if (vessel_id) {
					$.ajax({
						url: 'getVesselData.php',
						type: 'GET',
						data: {
							vessel_id: vessel_id
						},
						success: function(response) {
							var data = JSON.parse(response);

							if (data) {
								$('#owner_edit').val(data.owner_id);
								$('#vessel-name_edit').val(data.vessel_name);
								$('#origin_edit').val(data.vessel_origin);

								$('#vessel-name_edit').prop('disabled', false);
								$('#origin_edit').prop('disabled', false);
								$('#save-btn').prop('disabled', false);
								$('button[type="reset"]').prop('disabled', false);
							}
						},
						error: function() {
							alert('Error fetching vessel data');
						}
					});
				}
			});
		});
	</script>

	<section id="content">
		<nav>
			<i class='bx bx-menu'></i>
			<form action="#">
				<!--<div class="form-input">
					<input type="search" placeholder="Search...">
					<button type="submit" class="search-btn"><i class='bx bx-search' ></i></button>
				</div>-->
			</form>
			<!-- <input type="checkbox" id="switch-mode" hidden>
			<label for="switch-mode" class="switch-mode"></label> -->
			<a href="#" class="notification">
				<!--<i class='bx bxs-bell' ></i>
				<span class="num">8</span>-->
			</a>
			<a href="#" class="profile">
				<!-- <img src="img/people.png"> -->
			</a>
		</nav>

		<!-- dashboard admin -->
		<main id="dashboard" style="display: block;">
			<h2>Dashboard</h2>
			<div class="container mt-5" id="container-dashboard">
				<div class="row">
					<div class="col-md-12">
						<label for="yearSelect">Select Year:</label>
						<select id="yearSelect" class="form-control">
						</select>
					</div>
				</div>
				<div class="row mt-3">
					<div class="col-md-12 chart-container barchart-dashboard" id="barchart-display">
						<canvas id="barChart"></canvas>
					</div>
				</div>
			</div>
		</main>

		<!-- add edit vessels(admin) -->
		<main id="vessels" style="display: none;">
			<h2>Vessels</h2>
			<div class="container mt-5" id="container-vessels">
				<!-- Form Container -->
				<form id="vessel-form" method="post" action="addVessels.php">
					<h2>ADD VESSELS</h2>
					<div class="form-row mb-3">
						<div class="col-md-12">
							<label for="owner">Select Owner</label>
							<select class="form-control" id="owner" name="owner_id" required>
								<option value="" selected disabled>Select Owner</option>
								<?php
								$getOwnerData = "SELECT owner_id, owner_lname, owner_fname FROM tbl_owner";

								$result = mysqli_query($conn, $getOwnerData);

								if ($result) {
									while ($row = mysqli_fetch_assoc($result)) {
										$ownerName = $row['owner_fname'] . ' ' . $row['owner_lname'];
										echo "<option value='" . $row['owner_id'] . "'>" . $ownerName . "</option>";
									}
								} else {
									echo "<option value='' disabled>No owners found</option>";
								}
								?>
							</select>
						</div>
					</div>

					<div class="form-row mb-3">
						<div class="col-md-9">
							<label for="vessel-name">Vessel Name</label>
							<input type="text" class="form-control" id="vessel-name" name="vessel_name" placeholder="Enter Vessel Name" required>
						</div>
						<div class="col-md-3">
							<label for="origin">Origin</label>
							<input type="text" class="form-control" id="origin" name="origin" placeholder="Enter Origin" required>
						</div>
					</div>

					<div class="form-row mb-3">
						<div class="col-md-6">
							<button type="submit" class="btn btn-primary btn-block">Add Vessel</button>
						</div>
						<div class="col-md-6">
							<button type="reset" class="btn btn-secondary btn-block">Clear</button>
						</div>
					</div>
				</form>

				<br><br><br>

				<div class="row mb-4">
					<div class="col-md-12">
						<!-- Table to display the vessels -->
						<table class="table table-striped" id="vesselTable">
							<thead>
								<tr>
									<th scope="col">Vessel ID</th>
									<th scope="col">Owner Name</th>
									<th scope="col">Vessel Name</th>
									<th scope="col">Origin</th>
								</tr>
							</thead>
							<tbody>
								<!-- Table data will be injected here by AJAX -->
							</tbody>
						</table>

						<!-- Pagination Controls -->
						<nav aria-label="Vessel Pagination">
							<ul class="paginationVessels" id="paginationLinksVessels">
								<!-- Pagination buttons will be injected here by AJAX -->
							</ul>
						</nav>
					</div>
				</div>

				<br><br><br>

				<form action="updateVessel.php" method="post">
					<h2>EDIT VESSELS</h2>
					<div class="form-row mb-3">
						<div class="col-md-12">
							<label for="select_vessels_edit">Select Vessels</label>
							<select class="form-control" id="select_vessels_edit" name="vessel_id" required>
								<option value="" selected disabled>Select Vessels</option>
								<?php
								$getOwnerData = "SELECT vessel_id, vessel_name FROM tbl_vessel WHERE owner_id";
								$result = mysqli_query($conn, $getOwnerData);
								if ($result) {
									while ($row = mysqli_fetch_assoc($result)) {
										echo "<option value='" . $row['vessel_id'] . "'>" . $row['vessel_name'] . "</option>";
									}
								} else {
									echo "<option value='' disabled>No vessels found</option>";
								}
								?>
							</select>
						</div>
					</div>

					<div class="form-row mb-3">
						<div class="col-md-12">
							<label for="owner_edit">Select Owner</label>
							<select class="form-control" id="owner_edit" name="owner_id" disabled>
								<option value="" selected disabled>Select Owner</option>
								<?php
								$getOwnerData = "SELECT owner_id, owner_lname, owner_fname FROM tbl_owner";
								$result = mysqli_query($conn, $getOwnerData);
								if ($result) {
									while ($row = mysqli_fetch_assoc($result)) {
										$ownerName = $row['owner_fname'] . ' ' . $row['owner_lname'];
										$owner = $row['owner_id'];
										echo "<option value='" . $owner . "'>" . $ownerName . "</option>";
									}
								} else {
									echo "<option value='' disabled>No owners found</option>";
								}
								?>
							</select>
						</div>
					</div>

					<div class="form-row mb-3">
						<div class="col-md-9">
							<label for="vessel-name_edit">Vessel Name</label>
							<input type="text" class="form-control" id="vessel-name_edit" name="vessel_name" placeholder="Enter Vessel Name" required disabled>
						</div>
						<div class="col-md-3">
							<label for="origin">Origin</label>
							<input type="text" class="form-control" id="origin_edit" name="origin" placeholder="Enter Origin" required disabled>
						</div>
					</div>

					<div class="form-row mb-3">
						<div class="col-md-6">
							<button type="submit" class="btn btn-primary btn-block" id="save-btn" disabled>Save Changes</button>
						</div>
						<div class="col-md-6">
							<button type="reset" class="btn btn-secondary btn-block" disabled>Clear</button>
						</div>
					</div>
				</form>
			</div>
		</main>

		<!-- mag set ug price sa mga isda ug mag edit (admin) -->
		<main id="pricing" style="display: none;">
			<h2>Pricing</h2>
			<div class="container mt-5" id="container-pricing">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label for="vesselSelectPricing">Select Owner</label>
							<select class="form-control" id="vesselSelectPricing" name="vessel_id" required>
								<?php include 'getOwnerList.php'; ?>
							</select>
						</div>
					</div>
				</div>
				<br><br>
				<div class="row">
					<div class="col-12">
						<h3>Fish Catch Data</h3>
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>ID</th>
									<th>Vessel Name</th>
									<th>Departure</th>
									<th>Return</th>
								</tr>
							</thead>
							<tbody id="catch-data-body">
								<!-- diri mo gawas ang mga catch data -->
							</tbody>
						</table>
						<div id="pagination-controls" class="pagination-container">
							<!-- katong buttons nga mag select ka ug page -->
						</div>
					</div>
				</div>
				<br>
				<br>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label for="selectCatchID">Select Catch ID</label>
							<select class="form-control" id="selectCatchID" name="catch_id" required>
								<option value="" disabled selected>Select Catch ID</option>
							</select>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<h3>Fish Data</h3>
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>FISH NAME</th>
									<th>UNIT</th>
									<th>PRICE</th>
									<th>QUANTITY</th>
									<th>ACTION</th>
								</tr>
							</thead>
							<tbody id="fish-data-body">
								<!-- diri mo gawas ang mga fish data inag select sa catch data -->
							</tbody>
						</table>
						<div id="fish-pagination-controls" class="pagination-container">
							<!-- mga buttons sa pag select ug page-->
						</div>
					</div>
				</div>
			</div>
		</main>

		<!-- if mangisda na ang (owner) -->
		<main id="transaction" style="display: none;">
			<h2>Departure and Return</h2>
			<div class="container mt-5" id="container-transaction">
				<form action="departure.php" method="post">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="vesselSelect">Select a Vessel</label>

								<?php if ($status != "" || $status != null) { ?>
									<select class="form-control" id="vesselSelect" name="vessel_id" disabled>
										<option value="<?php echo htmlspecialchars($vessel_id); ?>" selected><?php echo htmlspecialchars($vessel_name); ?></option>
									</select>
								<?php } else { ?>
									<select class="form-control" id="vesselSelect" name="vessel_id" required>
										<?php include 'getVesselList.php'; ?>
									</select>
								<?php } ?>

							</div>
						</div>
					</div>
					<input type="hidden" name="catch_id" value="<?php echo htmlspecialchars($catch_id); ?>">

					<div class="row">
						<div class="col d-flex justify-content-start">
							<button class="btn btn-primary btn-lg mr-2 w-100" type="submit" name="departure">Departure</button>
							<button class="btn btn-danger btn-lg w-100" type="submit" name="return">Return</button>
						</div>
						<div class="col d-flex justify-content-center">
							<?php include 'departureDates.php'; ?>
						</div>
					</div>
				</form>

				<?php if ($status == "Return") { ?>
					<form action="addFishList.php" method="post">
						<input type="hidden" name="catch_id" value="<?php echo htmlspecialchars($catch_id); ?>">
						<div class="row mt-4">
							<div class="col-12">
								<div class="form-group">
									<label for="fishName">Fish Name</label>
									<input type="text" class="form-control" id="fishName" name="fishname" placeholder="Enter fish name" required>
								</div>
							</div>
						</div>
						<div class="row mt-4">
							<div class="col-12">
								<button type="submit" class="btn btn-success btn-lg w-100">Add to list</button>
							</div>
						</div>
					</form>
					<div class="row mt-4">
						<div class="col-12">
							<h3>Catched Fish List</h3>
							<table class="table table-bordered" style="border-color: transparent;">
								<thead>
									<tr>
										<th>Fish Name</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$sqlGetFishName = "SELECT fish_name FROM tbl_catched_fish WHERE catch_id = '$catch_id'";
									$result = mysqli_query($conn, $sqlGetFishName);

									if (!$result) {
										echo "<p>Error: " . mysqli_error($conn) . "</p>";
										exit;
									}
									if (mysqli_num_rows($result) > 0) {
										while ($row = mysqli_fetch_assoc($result)) {
											echo "<tr><td>" . htmlspecialchars($row['fish_name']) . "</td></tr>";
										}
									} else {
										echo "<tr><td colspan='1'>No fish found for this catch.</td></tr>";
									}
									?>
								</tbody>
							</table>
						</div>
					</div>
					<div class="col-12">
						<form action="doneTransaction.php" method="post">
							<input type="hidden" name="owner_id" value="<?php echo $_SESSION['id']; ?>">
							<button type="submit" class="btn btn-primary btn-lg w-100">DONE</button>
						</form>
					</div>
				<?php } ?>
			</div>
		</main>

		<!-- catch report sa (owner) -->
		<main id="catchData" style="display: none;">
			<h2>Fish Catch Data</h2>
			<div class="container mt-5" id="container-catchdata">
				<form id="catchDataForm">
					<div class="row">
						<!-- <div class="col-md-6">
							<div class="form-group">
								<label for="periodSelect">Select Period</label>
								<select class="form-control" id="periodSelect" required>
									<option value="all">All</option>
									<option value="monthly">Monthly</option>
									<option value="yearly">Yearly</option>
								</select>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label for="monthYearSelect">Select Month/Year</label>
								<select class="form-control" id="monthYearSelect" disabled>
									
								</select>
							</div>
						</div> -->
					</div>

					<div class="row mt-4">
						<div class="col-12">
							<h3>Fish Catch Data</h3>
							<table id="fishCatchTable" class="table table-bordered">
								<thead>
									<tr>
										<th>ID</th>
										<th>Vessel Name</th>
										<th>Departure</th>
										<th>Return</th>
									</tr>
								</thead>
								<tbody>
									<!-- table para sa mga catch report sa owner nga nag login -->
								</tbody>
							</table>

							<!-- Pagination Controls -->
							<div id="paginationControls" class="pagination">
								<!-- button sa mga pag change sa page -->
							</div>
						</div>
						<div class="col-12">
							<h3>Fish List</h3>
							<div class="col-md-12">
								<div class="form-group">
									<label for="selectCatchIdOwner">Select Catch ID</label>
									<select class="form-control" id="selectCatchIdOwner">
										<option value="" selected disabled>Select ID</option>
										<?php
										$catchIDOption = "SELECT c.catch_id 
                                  FROM tbl_catch_report c 
                                  INNER JOIN tbl_vessel v ON c.vessel_id = v.vessel_id 
                                  INNER JOIN tbl_owner o ON v.owner_id = o.owner_id 
                                  WHERE v.owner_id = '$owner_id'";

										$result = mysqli_query($conn, $catchIDOption);
										if (mysqli_num_rows($result) > 0) {
											while ($row = mysqli_fetch_assoc($result)) {
												echo "<option value='" . htmlspecialchars($row['catch_id']) . "' >" . htmlspecialchars($row['catch_id']) . "</option>";
											}
										} else {
											echo "<option value='' disabled>No Catch ID Yet</option>";
										}
										?>
									</select>
								</div>
							</div>

							<table class="table table-bordered">
								<thead>
									<tr>
										<th>Fish Name</th>
										<th>Price</th>
										<th>Unit</th>
										<th>Quantity</th>
									</tr>
								</thead>
								<tbody id="fish-list">
								</tbody>
							</table>

							<nav aria-label="Fish List Pagination">
								<ul class="pagination justify-content-center" id="pagination"></ul>
							</nav>
						</div>
					</div>
				</form>
			</div>
		</main>

		<main id="catchReport" style="display: none;">
			<h2>Buyer Data</h2>
			<div class="container mt-5" id="container-buyerdata">
				<form id="buyerDataForm">
					<div class="row mt-4">
						<div class="col-12">
							<h3>Buyer Data</h3>
							<table class="table table-bordered">
								<thead>
									<tr>
										<th>Transaction ID</th>
										<th>Buyer</th>
										<th>Address</th>
										<th>Phone Number</th>
										<th>Fish</th>
										<th>Unit</th>
										<th>Price</th>
										<th>Quantity</th>
										<th>Income</th>
										<th>Date</th>
									</tr>
								</thead>
								<tbody id="buyerDataTable">
									<?php
									$getBuyerData = "SELECT fl.sell_fish_list_id, fl.buy_quantity, 
															cf.fish_name, cf.unit, cf.price, 
															s.buyer_name, s.buyer_address, s.buyer_phonenumber, 
															s.date_bought
													FROM tbl_sell_fish_list fl 
													LEFT JOIN tbl_sell s ON fl.sell_id = s.sell_id 
													LEFT JOIN tbl_catched_fish cf ON fl.catched_fish_id = cf.catched_fish_id 
													LEFT JOIN tbl_catch_report cr ON cf.catch_id = cr.catch_id 
													LEFT JOIN tbl_vessel v ON cr.vessel_id = v.vessel_id 
													LEFT JOIN tbl_owner o ON v.owner_id = o.owner_id 
													WHERE o.owner_id = '$owner_id'";

									$result = mysqli_query($conn, $getBuyerData);

									$totalIncome = 0;

									if (mysqli_num_rows($result) > 0) {
										while ($row = mysqli_fetch_assoc($result)) {
											$transactionId = $row['sell_fish_list_id'];
											$buyerName = $row['buyer_name'];
											$buyerAddress = $row['buyer_address'];
											$buyerPhone = $row['buyer_phonenumber'];
											$fishName = $row['fish_name'];
											$unit = $row['unit'];
											$price = $row['price'];
											$quantity = $row['buy_quantity'];
											$income = $quantity * $price;
											$date = $row['date_bought'];

											// Add income to the total
											$totalIncome += $income;
									?>
											<tr>
												<td><?php echo $transactionId; ?></td>
												<td><?php echo $buyerName; ?></td>
												<td><?php echo $buyerAddress; ?></td>
												<td><?php echo $buyerPhone; ?></td>
												<td><?php echo $fishName; ?></td>
												<td><?php echo $unit; ?></td>
												<td><?php echo '₱' . number_format($price, 2); ?></td>
												<td><?php echo $quantity; ?></td>
												<td><?php echo '₱' . number_format($income, 2); ?></td>
												<td><?php echo date('F j, Y \a\t g:i A', strtotime($date)); ?></td>
											</tr>
									<?php
										}
									} else {
										echo "<tr><td colspan='10' class='text-center'>No records found</td></tr>";
									}
									?>
									<tr>
										<td colspan="8" class="text-right"><strong>Total Income</strong></td>
										<td id="totalIncome"><?php echo '₱' . number_format($totalIncome, 2); ?></td>
										<td></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</form>
			</div>
		</main>

		<!-- edit sa details (admin and owner) -->
		<main id="settings" style="display: none;">
			<h2>SETTINGS</h2>
			<div class="container mt-5">
				<h2>User Details</h2>
				<form action="saveSettings.php" method="post">
					<div class="form-row mb-3">
						<div class="col">
							<label for="username">Username</label>
							<input type="text" class="form-control" id="username" placeholder="Enter username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
						</div>
						<div class="col">
							<label for="password">Password</label>
							<input type="password" class="form-control" id="password" placeholder="Enter password" name="password" value="<?php echo htmlspecialchars($password); ?>" required>
						</div>
					</div>

					<div class="form-row mb-3">
						<div class="col">
							<label for="firstName">First Name</label>
							<input type="text" class="form-control" id="firstName" placeholder="Enter first name" name="firstname" value="<?php echo htmlspecialchars($firstname); ?>" required>
						</div>
						<div class="col">
							<label for="middleName">Middle Name</label>
							<input type="text" class="form-control" id="middleName" placeholder="Enter middle name" name="middlename" value="<?php echo htmlspecialchars($middlename); ?>" required>
						</div>
						<div class="col">
							<label for="lastName">Last Name</label>
							<input type="text" class="form-control" id="lastName" placeholder="Enter last name" name="lastname" value="<?php echo htmlspecialchars($lastname); ?>" required>
						</div>
					</div>

					<div class="form-row mb-3">
						<div class="col-9">
							<label for="address">Address</label>
							<input type="text" class="form-control" id="address" placeholder="Enter address" name="address" value="<?php echo htmlspecialchars($address); ?>" required>
						</div>
						<div class="col-3">
							<label for="phone">Phone Number</label>
							<input type="tel" class="form-control" id="phone" placeholder="Enter phone number" name="phonenumber" value="<?php echo htmlspecialchars($phonenumber); ?>" required>
						</div>
						<input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
					</div>

					<button type="submit" class="btn btn-primary">Save</button>
				</form>
			</div>

			<?php if (isset($_SESSION['message'])): ?>
				<script>
					sidebarClick('settings');
				</script>
				<div class="alert alert-success">
					<?php
					echo htmlspecialchars($_SESSION['message']);
					unset($_SESSION['message'])
					?>
				</div>
			<?php endif; ?>

			<?php if (isset($_SESSION['error'])): ?>
				<script>
					sidebarClick('settings');
				</script>
				<div class="alert alert-danger">
					<?php
					echo htmlspecialchars($_SESSION['error']);
					unset($_SESSION['error']);
					?>
				</div>
			<?php endif; ?>

		</main>

		<!-- inag mo palit na ang buyer -->
		<main id="selling" style="display: none;">
			<h2>CART</h2>

			<?php
			if ($hasCart) {
			?>
				<div class="container mt-5">
					<form id="confirmPaymentForm" method="POST">
						<div id="fishCartTable">
							<h4>Fish Cart</h4>
							<table class="table table-bordered">
								<thead>
									<tr>
										<th>Fish Name</th>
										<th>Unit</th>
										<th>Quantity (Buy)</th>
										<th>Price</th>
										<th>Total</th>
									</tr>
								</thead>
								<tbody id="fishCartTableBody">
									<?php
									$getCartList = "SELECT f.fish_name, f.unit, l.buy_quantity, f.price 
                                    FROM tbl_sell_fish_list l 
                                    LEFT JOIN tbl_catched_fish f ON l.catched_fish_id = f.catched_fish_id 
                                    WHERE l.sell_id = '$sell_id'";

									$result = mysqli_query($conn, $getCartList);

									$totalPrice = 0;

									while ($row = mysqli_fetch_assoc($result)) {
										$fishName = $row['fish_name'];
										$unit = $row['unit'];
										$quantity = $row['buy_quantity'];
										$price = $row['price'];
										$total = $quantity * $price;

										$totalPrice += $total;
									?>
										<tr>
											<td><?php echo $fishName; ?></td>
											<td><?php echo $unit; ?></td>
											<td><?php echo $quantity; ?></td>
											<td><?php echo '₱' . number_format($price, 2); ?></td>
											<td><?php echo '₱' . number_format($total, 2); ?></td>
										</tr>
									<?php
									}
									?>
									<tr>
										<td colspan="4" style="text-align: right;"><strong>Total Price</strong></td>
										<td><strong><?php echo '₱' . number_format($totalPrice, 2); ?></strong></td>
									</tr>
								</tbody>
							</table>
						</div>

						<div id="fishCartBuyerInfo" class="mt-4">
							<h5>Buyer Information</h5>
							<div class="row">
								<div class="col-md-6">
									<label for="fishCartBuyerName">Buyer Name:</label>
									<input type="text" id="fishCartBuyerName" name="buyer_name" class="form-control" placeholder="Enter buyer name" required>
								</div>
								<div class="col-md-3">
									<label for="fishCartAddress">Address:</label>
									<input type="text" id="fishCartAddress" name="buyer_address" class="form-control" placeholder="Enter address" required>
								</div>
								<div class="col-md-3">
									<label for="fishCartPhone">Phone Number:</label>
									<input type="text" id="fishCartPhone" name="buyer_phone" class="form-control" placeholder="Enter phone number" required>
								</div>
							</div>
						</div>

						<div class="mt-4">
							<button class="btn btn-secondary" id="fishCartCancelBtn" type="reset">Cancel</button>
							<button class="btn btn-primary" id="fishCartConfirmBtn" type="submit">Confirm Payment</button>
						</div>
					</form>
				</div>

				<br>

				<h2>LIST OF FISH</h2>
				<div class="container mt-5">
					<div class="row mb-4">
						<div class="col-md-12">
							<input type="text" id="searchInput" class="form-control" placeholder="Search for fish items..." />
						</div>
					</div>

					<div class="row" id="fishItems">
						<!-- list of fish sell -->
					</div>

					<div class="pagination justify-content-center mt-4" id="pagination-sellFish">
						<!-- pagination buttons -->
					</div>
				</div>
			<?php
			} else {
			?>
				<div class="container mt-5">
					<form action="getACart.php" method="post">
						<div class="col-md-12">
							<button class="btn btn-primary w-100" id="getACart" type="submit">Get A Cart</button>
						</div>
					</form>
				</div>
			<?php
			}
			?>
		</main>

		<!-- manage sa mga owner(Admin) -->
		<main id="usermanager" style="display: block;">
			<h2 class="text-center mt-4">Owner Manager</h2>
			<div class="container mt-5 userManagerContainer">
				<div class="row mb-3">
					<div class="col">
						<button class="btn btn-primary" id="addOwnerBtn" data-toggle="modal" data-target="#addOwnerModal">Add Owner</button>
					</div>
				</div>

				<div class="row">
					<div class="col">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>First Name</th>
									<th>Middle Name</th>
									<th>Last Name</th>
									<th>Phone</th>
									<th>Address</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$getOwnerDataList = "SELECT owner_id, owner_lname, owner_fname, owner_mname, phonenum, address FROM tbl_owner";
								$result = $conn->query($getOwnerDataList);

								if ($result->num_rows > 0) {
									while ($row = $result->fetch_assoc()) {
										echo "<tr>";
										echo "<td>" . $row['owner_fname'] . "</td>";
										echo "<td>" . $row['owner_mname'] . "</td>";
										echo "<td>" . $row['owner_lname'] . "</td>";
										echo "<td>" . $row['phonenum'] . "</td>";
										echo "<td>" . $row['address'] . "</td>";
										echo "<td>
												<button class='btn btn-secondary btn-sm edit-btn' data-toggle='modal' 
												data-target='#editOwnerModal' 
												data-id='" . $row['owner_id'] . "' 
												data-fname='" . $row['owner_fname'] . "' 
												data-mname='" . $row['owner_mname'] . "' 
												data-lname='" . $row['owner_lname'] . "' 
												data-phone='" . $row['phonenum'] . "' 
												data-address='" . $row['address'] . "'>Edit</button>

                                        		<button class='btn btn-danger btn-sm delete-btn' data-toggle='modal' 
												data-target='#deleteOwnerModal' 
												data-id='" . $row['owner_id'] . "' 
												data-fname='" . $row['owner_fname'] . "' 
												data-lname='" . $row['owner_lname'] . "'>Delete</button>
											</td>";
										echo "</tr>";
									}
								} else {
									// If no data is found
									echo "<tr><td colspan='7' class='text-center'>No owners found found</td></tr>";
								}
								?>
							</tbody>
						</table>

						<?php
						?>
					</div>
				</div>
			</div>
		</main>

	</section>

	<script src="script.js"></script>
	<script src="catchdata.js"></script>
	<script src="buyerdata.js"></script>
	<script src="transaction.js"></script>
	<!-- jQuery and Bootstrap JS -->
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
	


	<!-- pag search sa isda -->
	<script>
		$(document).ready(function() {
			const sellId = "<?php echo $sell_id; ?>";

			function loadFishData(page = 1) {
				const searchQuery = $('#searchInput').val();

				$.ajax({
					url: 'fish_sell_list.php',
					type: 'GET',
					data: {
						page: page,
						search: searchQuery,
						sell_id: sellId
					},
					success: function(response) {
						const data = JSON.parse(response);

						$('#fishItems').html(data.fish_items);
						$('#pagination-sellFish').html(data.pagination);
					},
					error: function() {
						alert("Error loading data.");
					}
				});
			}

			loadFishData();

			$('#searchInput').on('keyup', function() {
				loadFishData();
			});

			$(document).on('click', '.pagination-link-sellFish-page', function(e) {
				e.preventDefault();
				const page = $(this).data('page');
				loadFishData(page);
			});
		});
	</script>

	<!-- pag palit na ug isda, set ug quantity -->
	<script>
		$(document).ready(function() {
			$(document).on('click', '.increase-btn', function() {
				const cfId = $(this).data('cf-id');
				const maxQuantity = $(this).data('quantity');
				increaseQuantity(cfId, maxQuantity);
			});

			$(document).on('click', '.decrease-btn', function() {
				const cfId = $(this).data('cf-id');
				decreaseQuantity(cfId);
			});

			$(document).on('input', '.quantityClass', function() {
				const cfId = $(this).data('cf-id');
				const max = $(this).data('quantity-max');
				checkMaxQuantity(cfId, max);
			});

			function increaseQuantity(cfId, maxQuantity) {
				let input = document.getElementById('quantity' + cfId);
				let currentValue = parseInt(input.value);

				if (currentValue < maxQuantity) {
					input.value = currentValue + 1;
				}
			}

			function decreaseQuantity(cfId) {
				let input = document.getElementById('quantity' + cfId);
				let currentValue = parseInt(input.value);

				if (currentValue > 1) {
					input.value = currentValue - 1;
				}
			}

			function checkMaxQuantity(cfId, maxQuantity) {
				let input = document.getElementById('quantity' + cfId);
				let inputValue = parseInt(input.value);

				if (inputValue > maxQuantity) {
					input.value = maxQuantity;
				} else if (inputValue < 1) {
					input.value = 1;
				}
			}
		});
	</script>

	<!-- cancel sa cart -->
	<script>
		document.getElementById('fishCartCancelBtn').addEventListener('click', function(e) {
			e.preventDefault();

			var sellId = "<?php echo $sell_id; ?>";

			var xhr = new XMLHttpRequest();
			xhr.open('POST', 'confirmOrCancelPayment.php', true);
			xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			xhr.onload = function() {
				if (xhr.status === 200) {
					alert('Cancel Successfully!');
					window.location.reload();
				} else {
					alert('Error cancelling the cart.');
				}
			};

			xhr.send('sell_id=' + encodeURIComponent(sellId));
		});
	</script>

	<!-- pag confirm na sa payment -->
	<script>
		document.getElementById('confirmPaymentForm').addEventListener('submit', function(e) {
			e.preventDefault();

			var sellId = "<?php echo $sell_id; ?>";
			var buyerName = document.getElementById('fishCartBuyerName').value;
			var buyerAddress = document.getElementById('fishCartAddress').value;
			var buyerPhone = document.getElementById('fishCartPhone').value;

			var totalPrice = <?php echo $totalPrice; ?>;

			var xhr = new XMLHttpRequest();
			xhr.open('POST', 'confirmPayment.php', true);
			xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			xhr.onload = function() {
				if (xhr.status === 200) {
					alert('Payment Confirmed!');
					window.location.reload();
				} else {
					alert('Error confirming the payment.');
				}
			};

			xhr.send('sell_id=' + encodeURIComponent(sellId) +
				'&buyer_name=' + encodeURIComponent(buyerName) +
				'&buyer_address=' + encodeURIComponent(buyerAddress) +
				'&buyer_phone=' + encodeURIComponent(buyerPhone) +
				'&total_price=' + encodeURIComponent(totalPrice));
		});
	</script>

	<!-- sa owner na side sa fish catch data table -->
	<script>
		function loadFishCatchPage(page) {
			var owner_id = "<?php echo $owner_id; ?>";

			console.log('Owner ID:', owner_id);
			console.log('Page:', page);

			$.ajax({
				url: 'fetch_fish_catch_data.php',
				type: 'GET',
				data: {
					owner_id: owner_id,
					page: page
				},
				dataType: 'json',
				success: function(response) {
					console.log("AJAX Response:", response);
					// Always update the table data
					$('#fishCatchTable tbody').html(response.data);

					// Only update pagination if needed
					if (response.pagination) {
						$('#paginationControls').html(response.pagination);
					} else {
						$('#paginationControls').empty(); // If no pagination is needed
					}
				},
				error: function() {
					console.log("Error occurred while loading data");
				}
			});
		}

		$(document).ready(function() {
			loadFishCatchPage(1);

			$(document).on('click', '.pagination-link', function(e) {
				e.preventDefault();

				var page = $(this).data('page');
				loadFishCatchPage(page);
			});
		});
	</script>

	<!-- select ug year sa dashboard -->
	<script>
		function createOrUpdateChart(incomeData) {
			const ctx = document.getElementById('barChart').getContext('2d');

			if (window.barChart && window.barChart.destroy) {
				window.barChart.destroy();
			}

			window.barChart = new Chart(ctx, {
				type: 'bar',
				data: {
					labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
					datasets: [{
						label: 'Monthly Income',
						data: incomeData,
						backgroundColor: 'rgba(75, 192, 192, 0.2)',
						borderColor: 'rgba(75, 192, 192, 1)',
						borderWidth: 1
					}]
				},
				options: {
					scales: {
						y: {
							beginAtZero: true
						}
					}
				}
			});
		}

		function fetchTotalIncomeData(year) {
			$.ajax({
				url: 'getTotalIncome.php',
				type: 'GET',
				data: {
					year: year
				},
				success: function(response) {
					const incomeData = response.split(',').map(Number);

					if (incomeData.length === 12) {
						createOrUpdateChart(incomeData);
					} else {
						console.error('Invalid data received from server:', response);
					}
				},
				error: function() {
					alert('Error fetching data');
				}
			});
		}

		$(document).ready(function() {
			const yearSelect = $('#yearSelect');

			const currentYear = new Date().getFullYear();
			for (let year = currentYear; year >= currentYear - 10; year--) {
				yearSelect.append(new Option(year, year, year === currentYear, year === currentYear));
			}

			const selectedYear = yearSelect.val();

			fetchTotalIncomeData(selectedYear);

			yearSelect.change(function() {
				const newYear = $(this).val();
				fetchTotalIncomeData(newYear);
			});
		});
	</script>

	<!-- edit ug delete sa modal (admin) -->
	<script>
		$(document).ready(function() {
			$('#editOwnerModal').on('show.bs.modal', function(event) {
				var button = $(event.relatedTarget);
				var ownerId = button.data('id');
				var firstName = button.data('fname');
				var middleName = button.data('mname');
				var lastName = button.data('lname');
				var phone = button.data('phone');
				var address = button.data('address');

				var modal = $(this);
				modal.find('#editOwnerId').val(ownerId);
				modal.find('#editFirstName').val(firstName);
				modal.find('#editMiddleName').val(middleName);
				modal.find('#editLastName').val(lastName);
				modal.find('#editPhoneNumber').val(phone);
				modal.find('#editAddress').val(address);
			});

			$('#deleteOwnerModal').on('show.bs.modal', function(event) {
				var button = $(event.relatedTarget); 
				var ownerId = button.data('id');
				var firstName = button.data('fname');
				var lastName = button.data('lname');

				var modal = $(this);
				modal.find('#deleteOwnerId').val(ownerId);
				modal.find('#deleteOwnerMessage').text('Are you sure you want to delete ' + firstName + ' ' + lastName + '?');
			});

		});
	</script>

</body>

</html>